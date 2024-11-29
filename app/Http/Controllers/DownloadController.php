<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class DownloadController extends Controller
{

    private function getScoolExamplePath(): string
    {
        if (config('features.excel_headers')) {
            return sprintf('%s/data/SchoolDataWithHeaders.xlsx', base_path());
        }

        return sprintf('%s/data/SchoolData.xlsx', base_path());
    }

    public function downloadSchoolExample(): \Symfony\Component\HttpFoundation\Response
    {
        return response()->download($this->getScoolExamplePath(), 'SchoolData.xlsx');
    }

    private function getRosterSubjectsExamplePath(): string
    {
        return sprintf('%s/data/roster/roster_subjects_example.xlsx', base_path());
    }

    public function downloadRosterSubjectsExample(): \Symfony\Component\HttpFoundation\Response
    {
        return response()->download($this->getRosterSubjectsExamplePath(), 'roster_subjects_example.xlsx');
    }


    private function getRosterScheduleExamplePath(): string
    {
        return sprintf('%s/data/roster/roster_schedule_example.xlsx', base_path());
    }

    public function downloadRosterScheduleExample(): \Symfony\Component\HttpFoundation\Response
    {
        return response()->download($this->getRosterScheduleExamplePath(), 'roster_schedule_example.xlsx');
    }

    private function getRosterPreferredTimings1Path(): string
    {
        return sprintf('%s/data/roster/roster_preferred_timings1_example.xlsx', base_path());
    }

    public function downloadPreferredTimings1Example(): \Symfony\Component\HttpFoundation\Response
    {
        return response()->download($this->getRosterPreferredTimings1Path(), 'roster_preferred_timings_example.xlsx');
    }

    private function getRosterPreferredTimings2Path(): string
    {
        return sprintf('%s/data/roster/roster_preferred_timings2_example.xlsx', base_path());
    }

    public function downloadPreferredTimings2Example(): \Symfony\Component\HttpFoundation\Response
    {
        return response()->download($this->getRosterPreferredTimings2Path(), 'roster_preferred_timings2_example.xlsx');
    }

    public function view(): Response
    {
        return Inertia::render(
            'Downloads/View',
            [
            ]
        );
    }
}