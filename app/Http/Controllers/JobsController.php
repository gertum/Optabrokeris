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
        if ($job == null ) {
            $jobObj = null;
        }
        else {
            $jobObj = Job::query()->findOrFail(['id' => $job])->first();
        }

        return Inertia::render('Jobs/View', [
            'job' => $jobObj,
        ]);
    }
}
