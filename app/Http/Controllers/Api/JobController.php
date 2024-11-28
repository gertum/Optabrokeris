<?php

namespace App\Http\Controllers\Api;

use App\Domain\Roster\Hospital\ScheduleParser;
use App\Domain\Roster\Profile;
use App\Exceptions\SolverException;
use App\Exceptions\ValidateException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Job\JobRequest;
use App\Http\Requests\Job\JobSolveRequest;
use App\Http\Requests\Job\JobUploadRequest;
use App\Models\Job;
use App\Models\User;
use App\Repositories\JobRepository;
use App\Repositories\SubjectRepository;
use App\Solver\SolverClientFactory;
use App\Transformers\SpreadSheetHandlerFactory;
use DateTime;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class JobController extends Controller
{
    private SolverClientFactory $solverClientFactory;

    public function __construct(SolverClientFactory $solverClientFactory)
    {
        $this->solverClientFactory = $solverClientFactory;
    }

    public function list(Request $request, JobRepository $jobRepository)
    {
        $userId = $request->user()->id;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);

        return $jobRepository->getJobList($userId, $offset, $limit);
    }

    public function view(Request $request, Job $job)
    {
        $this->tryToGetResultFromSolver($job);

//        $this->prepareForBautifulJson($job);

        // lets try to put the parsed result content


        return $job;
    }


    protected function tryToGetResultFromSolver(Job $job)
    {
        try {
            $type = $job->getAttribute('type');

            $solverClient = $this->solverClientFactory->createClient($type);
            $result = $solverClient->getResult($job->getSolverId());

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
                $job->update(
                    ['result' => $result, 'status' => $status, 'flag_solved' => $flagSolved, 'flag_solving' => 0]
                );
            }
        } catch (Exception $e) {
            $job->setResult(null);
            $job->setErrorMessage($e->getMessage());
            Log::error($e->getMessage());
        }

        return $job;
    }

    /**
     * @param Job $job
     * @deprecated same properties used both for string and object data
     */
    public function prepareForBautifulJson(Job $job)
    {
        $data = $job->getData();
        $result = $job->getResult();

        // for beautifull json
        if ($data != null) {
            $job->setData(json_decode($data));
        }

        if ($result != null) {
            $job->setResult(json_decode($result));
        }
    }

    public function create(Request $request, JobRepository $jobRepository)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required'],
                'type' => [
                    'required',
                    Rule::in(SolverClientFactory::TYPES),
                ],

            ]
        );


        $validated = $validator->validate();
        // Vietoj 'validated' reikia naudoti 'validate', kitaip nemeta exceptiono

        $body = $request->getContent();

        /** @var User $user */
        $user = $request->user();

        $profile = $user->getProfile();

        // default profile if null
        if ($profile == null) {
            $profileObj = new Profile();
            $profileObj->setShiftBounds([8, 20]);
            $profile = json_encode($profileObj);
        }

        $validated['data'] = $body;
        $validated['user_id'] = $user->getKey();
        $validated['solver_id'] = 0;
        $validated['status'] = '';
        $validated['result'] = '';
        $validated['profile'] = $profile;

        /** @var Job $existingJob */
        $existingJob = $jobRepository->findJobByName($validated['name']);

        if ($existingJob != null) {
            throw new ValidateException(
                sprintf(
                    'There already is a job with the same name %s, with id %s',
                    $existingJob->getName(),
                    $existingJob->getKey()
                )
            );
        }


        $job = Job::query()->newModelInstance();
        $createdJob = $job->create($validated);

        return $createdJob;
    }

    public function update(JobRequest $request, Job $job)
    {
        $data = $request->validated();
        $job->update($data);

        return $job;
    }

    public function solve(JobSolveRequest $request, Job $job)
    {
        $solverClient = $this->solverClientFactory->createClient($job->type);
        $repeat = $request->get('repeat', false);

        try {
            if (!$repeat) {
                $data = $job->getData();
                $solverId = $solverClient->registerData($data);
                $job->update(['solver_id' => $solverId]);
            }

            $solvingResult = $solverClient->startSolving($job->solver_id);
            $job->update(['flag_solving' => true, 'flag_solved' => false]);
        } catch (GuzzleException $e) {
            $message = $e->getMessage();

            if (str_contains($message, 'cURL error')) {
                $message = "Can't connect to solver";
            }

            throw new SolverException($message);
        }


        return $solvingResult;
    }

    public function stop(Request $request, Job $job)
    {
        $solverClient = $this->solverClientFactory->createClient($job->getType());
        try {
            $solvingResult = $solverClient->stopSolving($job->solver_id);
        } catch (GuzzleException $e) {
            $message = $e->getMessage();

            if (str_contains($message, 'cURL error')) {
                $message = "Can't connect to solver";
            }

            throw new SolverException($message);
        }


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
        $profileObj = $job->getProfileObj();
        $profileObj->writeType = Profile::WRITE_TYPE_ORIGINAL_FILE;
        $job->setProfile(json_encode($profileObj));

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
        $fileHandler->arrayToSpreadSheet($dataArray, $file, $job);

        return response()->download($file, $fileName);
    }

    public function delete(Job $job)
    {
        $job->delete();

        return $job;
    }

    public function uploadPreferredXlsx(
        Request $request,
        Job $job,
        ScheduleParser $scheduleParser,
        SubjectRepository $subjectRepository
    ) {
        $xslxFile = $request->file('file');
        /** @var User $user */
        $user = $request->user();

        if ( $job->getUserId() != $user->getKey() ) {
            throw new AccessDeniedHttpException('Current user is not allowed to access this job');
        }

        $profileObj = $job->getProfileObj();
        if (count($profileObj->getShiftBounds()) == 0) {
            // setting default values for bounds, when bounds are not given
            $profileObj->setShiftBounds([8, 20]);
        }

        $schedule = $scheduleParser->parsePreferedScheduleXls($xslxFile->getRealPath(), $profileObj);

        $employeesNames = $schedule->getEmployeesNames();
        $subjects = $subjectRepository->loadSubjectsByNames($employeesNames);
        $schedule->fillEmployeesWithSubjectsData($subjects);

        $dataArray = $schedule->toArray();
        $job->setData(json_encode($dataArray));
        $profileObj = $job->getProfileObj();
        $profileObj->writeType = Profile::WRITE_TYPE_TEMPLATE_FILE;
        $job->setProfile(json_encode($profileObj));


        $job->setFlagUploaded(true);
        $job->setFlagSolving(false);
        $job->setFlagSolved(false);

        $job->save();

        return $job;
    }
}
