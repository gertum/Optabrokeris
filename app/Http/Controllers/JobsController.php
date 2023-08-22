<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JobsController extends Controller
{
    public function list(Request $request): Response
    {
        $jobs = Job::query()->get();

        return Inertia::render('Jobs', [
            'jobs' => $jobs
        ]);
    }

    public function view(Request $request, $id) {
        $job = Job::query()->find($id);

        return Inertia::render('Job', [
            'job' => $job
        ]);

    }

    public function newJob(Request $request): Response
    {
        return Inertia::render('NewJob');
    }

    public function testUpload(Request $request) {
        return view('testupload');
    }

public function editJob($id, $type)
{
    $job = Job::query()->find($id);

    return Inertia::render('EditJob', [
        'job' => $job,
        'jobId' => $id,
        'jobType' => $type,
    ]);
}

}