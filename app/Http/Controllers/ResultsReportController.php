<?php

namespace App\Http\Controllers;

use App\Repositories\JobRepository;
use App\Transformers\Roster\AmbulanceJobReportTransformer;
use Illuminate\Http\Request;

/**
 * @deprecated this is a backend GUI controller, not finished.
 */
class ResultsReportController  extends Controller
{
    public function getResults(Request $request, JobRepository $jobRepository, AmbulanceJobReportTransformer $transformer) {

        $id = $request->get('id');

        if ( $id == null ) {
            return view("report_results_error", ['message' => 'id is not given'] );
        }

        $job = $jobRepository->getJob($id);

        if ( $job == null ) {
            return view("report_results_error", ['message' => sprintf('Could not load job by id %s', $id)] );
        }

        $report = $transformer->buildAmbulanceReport($job );

        return view("report_results", ['job' => $job, 'report' => $report ] );
    }
}