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
        return Inertia::render('Jobs/List', [
            'jobs' => []
        ]);
    }

    public function form(Job $job = null)
    {
        return Inertia::render('Jobs/Form', [
            'job' => $job  ? $job->toArray() : null,
        ]);
    }
}
