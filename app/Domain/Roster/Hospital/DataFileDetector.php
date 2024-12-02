<?php

namespace App\Domain\Roster\Hospital;

class DataFileDetector
{
    const TYPE_AVAILABILITIES_XLS = 'availabilities-xlsx';
    const TYPE_SCHEDULE_XLS = 'schedule-xlsx';
    const TYPE_SUBJECTS_XLS = 'subjects-xlsx';
    const TYPE_SCHOOL_XLS = 'school-xlsx';

    public function detectExcelType(string $file) : ?string {
        // TODO
        return null;
    }

}