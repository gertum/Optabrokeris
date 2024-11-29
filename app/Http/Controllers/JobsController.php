<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JobsController extends Controller
{
    // kažkodėl šitas pradėjo refreshintis, kai atkūriau DashboardController, kuris ima route /
    // o šitas prijungtas prie /jobs
    public function list(Request $request): Response
    {
        return Inertia::render('Jobs/List', [
            'jobs' => []
        ]);
    }

    // G.T. testuoju, ar šitas padės refreshinimui... kol kas nepadeda
    public function view(?int $job=null): Response
    {
        // the job content is loaded through API
//        if ($job == null ) {
//            $jobObj = null;
//        }
//        else {
//            $jobObj = Job::query()->findOrFail(['id' => $job])->first();
//        }

        $jobObj = (new Job())->setId($job);
        return Inertia::render('Jobs/View', [
            'job' => $jobObj,
        ]);
    }

    public function create(): Response {
        return Inertia::render('Jobs/Create', [
        ]);
    }
}
