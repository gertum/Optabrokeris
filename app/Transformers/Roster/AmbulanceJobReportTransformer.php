<?php

namespace App\Transformers\Roster;

use App\Domain\Roster\Report\ScheduleReport;
use App\Models\Job;

class AmbulanceJobReportTransformer
{

    public function buildAmbulanceReport(Job $job) : ScheduleReport {
        $report = new ScheduleReport();

        // TODO implement
        // TODO cover with test.

        $report->setId( $job->getKey());
        $report->setName( $job->getName());

        return $report;
    }
}