<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class DownloadController extends Controller
{
    public function downloadSchoolExample()
    {
        return response()->download($this->getScoolExamplePath(), 'SchoolData.xlsx');
    }

    private function getScoolExamplePath () : string {
        if ( config('features.excel_headers')) {
            return sprintf('%s/data/SchoolDataWithHeaders.xlsx', base_path());
        }

        return sprintf('%s/data/SchoolData.xlsx', base_path());
    }

    public function view() : Response {
        return Inertia::render('Downloads/View', [
        ]);
    }
}