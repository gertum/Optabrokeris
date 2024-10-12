<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ValidateException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Job\JobRequest;
use App\Http\Requests\Job\JobSolveRequest;
use App\Http\Requests\Job\JobUploadRequest;
use App\Models\Job;
use App\Solver\SolverClientFactory;
use App\Transformers\SpreadSheetHandlerFactory;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DateTime;

class JobController extends Controller
{
    private SolverClientFactory $solverClientFactory;

    public function __construct(SolverClientFactory $solverClientFactory)
    {
        $this->solverClientFactory = $solverClientFactory;
    }

    public function list(Request $request)
    {
        $userId = $request->user()->id;

        $jobs = Job::query()->orderByDesc('created_at')->user($userId)->get();

        return $jobs;
    }

    public function view(Request $request, Job $job)
    {
        $this->tryToGetResultFromSolver($job);

//        $this->prepareForBautifulJson($job);


        return $job;
    }


    protected  function tryToGetResultFromSolver(Job $job) {
        try {
            $type = $job->getAttribute('type');

            $solverClient = $this->solverClientFactory->createClient($type);
            $result = $solverClient->getResult($job->solver_id);

            $flagSolved = false;
            try {
                $resultDataArray = Utils::jsonDecode($result, true);
                $status = $resultDataArray['solverStatus'];

                if ($job->getFlagSolving() && $status == 'NOT_SOLVING') {
                    $flagSolved = true;
                }
                $job->update(['result' => $result, 'status' => $status, 'flag_solved' => $flagSolved]);
            } catch (GuzzleException $e) {
                Log::warning($e->getMessage());
                $status = 'error';
                $job->update(['result' => $result, 'status' => $status, 'flag_solved' => $flagSolved, 'flag_solving'=>0]);
            }

        } catch (Exception $e) {
            $job->setResult(null);
            $job->error_message = $e->getMessage();
            Log::error($e->getMessage());
        }

        return $job;

    }
    public function prepareForBautifulJson(Job $job) {
        $data = $job->getData();
        $result  = $job->getResult();

        // for beautifull json
        if ( $data != null ) {
            $job->setData(json_decode($data));
        }

        if ($result != null ) {
            $job->setResult(json_decode($result));
        }
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'type' => [
                'required',
                Rule::in(SolverClientFactory::TYPES),
            ],

        ]);


        $validated = $validator->validated();
        // laravelis gaidys, nes nurodžiau kad name 'required' (žr 10 eilučių aukščiau :  'name' => ['required'] ) , o jis neduoda klaidos, jeigu nepaduodu 'name' per requestą.
        if (!array_key_exists('name', $validated)) {
            throw new ValidateException('Required job parameter "name" is missing.');
        }

        $body = $request->getContent();

        $validated['data'] = $body;
        $validated['user_id'] = $request->user()->id;
        $validated['solver_id'] = 0;
        $validated['status'] = '';
        $validated['result'] = '';
        $job = Job::query()->newModelInstance();
        $createdJob = $job->create($validated);

        return $createdJob;
    }

    public function update(JobRequest $request, Job $job)
    {
        // TODO @Vytenis : po pakeitimo, kad Job $job būtų per parametrus, nustojo veikti mano padarytas apribojimas matyti ir redaguoti tik savo job'us.
        return $job->update($request->validated());
    }

    public function solve(JobSolveRequest $request, Job $job)
    {
        $solverClient = $this->solverClientFactory->createClient($job->type);
        $repeat = $request->get('repeat', false);

        if (!$repeat) {
            $data = $job->getData();
            $solverId = $solverClient->registerData($data);
            $job->update(['solver_id' => $solverId]);
        }

        $solvingResult = $solverClient->startSolving($job->solver_id);
        $job->update(['flag_solving' => true, 'flag_solved' => false]);

        return $solvingResult;
    }

    public function stop(Request $request, Job $job)
    {
        $solverClient = $this->solverClientFactory->createClient($job->type);
        $solvingResult = $solverClient->stopSolving($job->solver_id);

        $job->setFlagSolved(true);
        $job->save();

        return $solvingResult;
    }

    public function upload(JobUploadRequest $request, Job $job, SpreadSheetHandlerFactory $fileHandlerFactory)
    {
        $file = $request->file('file');
        $fileHandler = $fileHandlerFactory->createHandler($job->getType(), $file->getClientOriginalName());

        $dataArray = $fileHandler->spreadSheetToArray($file->getRealPath());
        $job->setOriginalFileContent(file_get_contents($file->getRealPath()));

        $fileHandler->validateDataArray($dataArray);
        $job->setData(json_encode($dataArray));

        $job->setFlagUploaded(true);
        $job->setFlagSolving(false);
        $job->setFlagSolved(false);

        $job->save();

//
//        $solverClient = $this->solverClientFactory->createClient($job->type);
//        $data = $job->getData();
//        $solverId = $solverClient->registerData($data);
//        $job->update(['solver_id' => $solverId]);
//        // --

        return $job;
    }

    public function download(Request $request, Job $job, SpreadSheetHandlerFactory $fileHandlerFactory)
    {
        $this->tryToGetResultFromSolver($job);

        $fileName = sprintf(
            '%s_result_%s_%s.xlsx',
            $job->getName(),
            $job->getKey(),
            (new DateTime())->format('Y-m-d_H-i-s')
        );

        $file = '/tmp/' . $fileName;

        $fileHandler = $fileHandlerFactory->createHandler($job->getType(), $file);

        $data = $job->getResult();

        // solution for development, when solver is not started (so the result is empty), we take data instead
        if (empty($data)) {
            $data = $job->getData();
        }

        $dataArray = Utils::jsonDecode($data, true);
        $fileHandler->arrayToSpreadSheet($dataArray, $file, $job->getOriginalFileContent());

        return response()->download($file, $fileName);
    }
}
