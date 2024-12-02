<?php

namespace App\Domain\Roster\Hospital;

use App\Exceptions\ExcelParseException;

class DataFileDetector
{
    const TYPE_AVAILABILITIES_XLS = 'availabilities-xlsx';
    const TYPE_SCHEDULE_XLS = 'schedule-xlsx';
    const TYPE_SUBJECTS_XLS = 'subjects-xlsx';
    const TYPE_SCHOOL_XLS = 'school-xlsx';

    private string $errorMessage = '';

    public function detectExcelType(string $file): ?string
    {
        if (!str_ends_with($file, '.xlsx')) {
            return null;
        }

        try {
            $wrapper = ExcelWrapper::parse($file);
        } catch (ExcelParseException $e) {
            $this->errorMessage = $e->getMessage();
            return null;
        }

        ScheduleParser::registerStandardMatchers($wrapper);
        ScheduleParser::registerSubjectMatchers($wrapper);
        ScheduleParser::registerAvailabilitiesMatchers($wrapper);
        $wrapper->runMatchers();

        // detect which are found and which are not a and make decisions

        if ($this->countAvailabilitiesMatchers($wrapper, ScheduleParser::SCHEDULE_MATCHERS_CRITICAL_KEYS) ==
            count(ScheduleParser::SCHEDULE_MATCHERS_CRITICAL_KEYS)
        ) {
            return self::TYPE_SCHEDULE_XLS;
        }

        // find x,xn,a,xd,p,d,d2,n lets say 50% of these symbols marks file as the availabilities preferences
        if ($this->countAvailabilitiesMatchers($wrapper, ScheduleParser::AVAILABILITIES_MATCHERS_KEYS) >=
            count(ScheduleParser::AVAILABILITIES_MATCHERS_KEYS) / 2
        ) {
            return self::TYPE_AVAILABILITIES_XLS;
        }

        if ($this->countAvailabilitiesMatchers($wrapper, ScheduleParser::SUBJECTS_MATCHERS_CRITICAL_KEYS) ==
            count(ScheduleParser::SUBJECTS_MATCHERS_CRITICAL_KEYS)
        ) {
            return self::TYPE_SUBJECTS_XLS;
        }

        return null;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    private function countAvailabilitiesMatchers(ExcelWrapper $wrapper, array $matchersKeys)
    {
        $count = 0;

        foreach ($matchersKeys as $symbol) {
            if ($wrapper->getMatcher($symbol)->getColumn() >= 0) {
                $count++;
            }
        }
        return $count;
    }
}