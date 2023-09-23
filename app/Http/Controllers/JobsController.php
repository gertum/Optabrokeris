<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JobsController extends Controller
{
    public function list(Request $request): Response
    {
        return Inertia::render('Jobs', [
            // front must take jobs from API
            'jobs' => []
        ]);
    }

    public function view(Request $request)
    {
        return Inertia::render('Job', [
            // front must get joby from api
        ]);

    }

    public function newJob(Request $request): Response
    {
        return Inertia::render('NewJob');
    }

    // this function is not needed anymore
//    public function testUpload(Request $request) {
//        return view('testupload');
//    }

    public function editJob($id, $type)
    {
        return Inertia::render('EditJob', [
            // front must get job from api
        ]);
    }

}