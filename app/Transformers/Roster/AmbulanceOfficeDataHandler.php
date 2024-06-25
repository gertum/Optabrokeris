<?php

namespace App\Transformers\Roster;

use App\Domain\Roster\Hospital\ScheduleParser;
use App\Domain\Roster\Schedule;
use App\Transformers\SpreadSheetDataHandler;

class AmbulanceOfficeDataHandler implements SpreadSheetDataHandler
{
    public function spreadSheetToArray(string $excelFile): array
    {
        $scheduleParser = new ScheduleParser();

        $schedule = $scheduleParser->parseScheduleXls($excelFile, ScheduleParser::createHospitalTimeSlices());

        return $schedule->toArray();
    }

    public function arrayToSpreadSheet(array $data, string $excelFile): void
    {

        $schedule = new Schedule($data);



        // not implemented yet
        // TODO: Implement arrayToSpreadSheet() method.
    }

    public function validateDataArray(array $data): void
    {
        // not implemented yet
        // TODO: Implement validateDataArray() method.
    }

}