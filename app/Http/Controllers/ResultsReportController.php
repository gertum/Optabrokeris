<?php

namespace App\Http\Controllers;

use App\Repositories\JobRepository;
use Illuminate\Http\Request;

class ResultsReportController  extends Controller
{
    public function getResults(Request $request, JobRepository $jobRepository) {

        $id = $request->get('id');

        if ( $id == null ) {
            return view("report_results_error", ['message' => 'id is not given'] );
        }

        $job = $jobRepository->getJob($id);

        if ( $job == null ) {
            return view("report_results_error", ['message' => sprintf('Could not load job by id %s', $id)] );
        }


        return view("report_results", ['job' => $job] );
    }
}