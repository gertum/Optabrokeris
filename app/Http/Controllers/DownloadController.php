<?php

namespace App\Http\Controllers;

class DownloadController extends Controller
{
    public function downloadSchoolExample()
    {
        $downloadExamplePath = sprintf('%s/data/SchoolData.xlsx', base_path());

        return response()->download($downloadExamplePath, 'SchoolData.xlsx');
    }
}