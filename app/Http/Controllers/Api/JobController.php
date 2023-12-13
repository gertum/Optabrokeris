<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Job\JobRequest;
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

        $jobs = Job::query()->orderBy('created_at')->user($userId)->get();

        return $jobs;
    }

    public function view(JobRequest $request, $id)
    {
        $job = $request->getUserJob($id);

        try {
            $type = $job->getAttribute('type');

            $solverClient = $this->solverClientFactory->createClient($type);
            $result = $solverClient->getResult($job->solver_id);

            $flagSovled = false;
            try {
                $resultDataArray = Utils::jsonDecode($result, true);
                $status = $resultDataArray['solverStatus'];

                if ($job->getFlagSolving() && $status == 'NOT_SOLVING') {
                    $flagSovled = true;
                }
            } catch (GuzzleException $e) {
                Log::warning($e->getMessage());
                $status = 'error';
            }
            $job->update(['result' => $result, 'status' => $status, 'flag_solved' => $flagSovled]);
            // TODO create migration for score column
        } catch (Exception $e) {
            $job->error_message = $e->getMessage();
            Log::error($e->getMessage());
        }

        return $job;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => [
                'required',
                Rule::in(SolverClientFactory::TYPES),
            ],
            'name' => ['required'],

        ]);

        $validated = $validator->validated();
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

    public function update(JobRequest $request, $id)
    {
        $job = $request->getUserJob($id);

        $body = $request->getContent();
        $validated['data'] = $body;

        $job->fill($validated);
        $job->update($validated);

        return $job;
    }

    public function solve(JobRequest $request, $id)
    {
        $job = $request->getUserJob($id);

        $solverClient = $this->solverClientFactory->createClient($job->type);
        $repeat = $request->get('repeat');

        if (!$repeat) {
            $solverId = $solverClient->registerData($job->data);
            $job->update(['solver_id' => $solverId]);
        }

        $solvingResult = $solverClient->startSolving($job->solver_id);
        $job->update(['flag_solving' => true, 'flag_solved' => false]);

        return $solvingResult;
    }

    public function stop(JobRequest $request, $id)
    {
        $job = $request->getUserJob($id);
        $solverClient = $this->solverClientFactory->createClient($job->type);
        $solvingResult = $solverClient->stopSolving($job->solver_id);

        $job->setFlagSolved(true);
        $job->save();

        return $solvingResult;
    }

    public function upload(JobRequest $request, $id, SpreadSheetHandlerFactory $fileHandlerFactory)
    {
        $job = $request->getUserJob($id);
        $file = $request->file('file');
        $fileHandler = $fileHandlerFactory->createHandler($job->getType(), $file->getClientOriginalName());

        $dataArray = $fileHandler->spreadSheetToArray($file->getRealPath());
        $fileHandler->validateDataArray($dataArray);
        $job->setData(json_encode($dataArray));

        $job->setFlagUploaded(true);
        $job->setFlagSolving(false);
        $job->setFlagSolved(false);

        $job->save();

        return $job;
    }

    public function download(JobRequest $request, $id, SpreadSheetHandlerFactory $fileHandlerFactory)
    {
        $job = $request->getUserJob($id);
        $fileName = sprintf('result_%s.xlsx', $id);

        $file = '/tmp/' . $fileName;

        $fileHandler = $fileHandlerFactory->createHandler($job->getType(), $file);

        $data = $job->getResult();

        // solution for development, when solver is not started (so the result is empty), we take data instead
        if (empty($data)) {
            $data = $job->getData();
        }

        $dataArray = Utils::jsonDecode($data, true);
        $fileHandler->arrayToSpreadSheet($dataArray, $file);

        return response()->download($file, $fileName);
    }
}
