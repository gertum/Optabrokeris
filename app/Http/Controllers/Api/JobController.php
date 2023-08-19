<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Solver\SolverClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
        return Job::query()->get();
    }

    public function view(Request $request, $id)
    {
        $job = Job::query()->find($id);
        try {

            $solverClient = $this->solverClientFactory->createClient($job->type);
            $result = $solverClient->getResult($job->solver_id);

            try {
                $resultDataArray = Utils::jsonDecode($result, true);
                $status = $resultDataArray['solverStatus'];
            } catch (GuzzleException $e) {
                Log::warning($e->getMessage());
                $status = 'error';
            }
            $job->update(['result' => $result, 'status' => $status]);

            // TODO create migration for score column
        }
        catch(\Exception $e)
        {
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
        ]);

        $validated = $validator->validated();
        $body = $request->getContent();

        $validated['data'] = $body;
        // TODO correct user id
        $validated['user_id'] = 1;
        $validated['solver_id'] = 0;
        $validated['status'] = '';
        $validated['result'] = '';
        $job = Job::query()->newModelInstance();
        $createdJob = $job->create($validated);

        return $createdJob;
    }

    public function update(Request $request, $id)
    {
        $body = $request->getContent();
        $validated['data'] = $body;
        $job = Job::query()->find($id);

        $job->fill($validated);
        $job->update($validated);

        return $job;
    }

    public function solve(Request $request, $id)
    {
        $job = Job::query()->find($id);
        $solverClient = $this->solverClientFactory->createClient($job->type);
        $repeat = $request->get('repeat');

        if ( !$repeat) {
            $solverId = $solverClient->registerData($job->data);
            $job->update(['solver_id' => $solverId]);
        }

        return $solverClient->startSolving($job->solver_id);
    }

    public function upload(Request $request, $id)
    {
        $file = $request->file('task');

//        //Display File Name
//        echo 'File Name: '.$file->getClientOriginalName();
//        echo '<br>';
//
//        //Display File Extension
//        echo 'File Extension: '.$file->getClientOriginalExtension();
//        echo '<br>';
//
//        //Display File Real Path
//        echo 'File Real Path: '.$file->getRealPath();
//        echo '<br>';
//
//        //Display File Size
//        echo 'File Size: '.$file->getSize();
//        echo '<br>';
//
//        //Display File Mime Type
//        echo 'File Mime Type: '.$file->getMimeType();

        Storage::put($file->getClientOriginalName(), file_get_contents($file->getRealPath()));

        $rez = [
            'name' => $file->getClientOriginalName()
        ];

        return $rez;
    }

    // TODO download
    public function download(Request $request, $id)
    {
        // TODO
        return ['id' => $id];
    }
}
