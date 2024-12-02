<?php

namespace App\Transformers;

use App\Domain\Roster\Profile;
use App\Models\Job;

interface SpreadSheetDataHandler
{
    // may be these two methods should be separated in to two classes

    public function spreadSheetToArray(string $excelFile, ?Profile $profileObj=null): array;

    public function arrayToSpreadSheet(array $data, string $excelFile, ?Job $job): void;

    public function validateDataArray(array $data): void;
}
