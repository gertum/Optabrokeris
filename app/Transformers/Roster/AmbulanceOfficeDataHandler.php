<?php

namespace App\Transformers\Roster;

use App\Domain\Roster\Hospital\ScheduleParser;
use App\Domain\Roster\Hospital\ScheduleWriter;
use App\Domain\Roster\Schedule;
use App\Transformers\SpreadSheetDataHandler;

class AmbulanceOfficeDataHandler implements SpreadSheetDataHandler
{

    private ScheduleWriter $scheduleWriter;

    /**
     * @param ScheduleWriter $scheduleWriter
     */
    public function __construct(ScheduleWriter $scheduleWriter)
    {
        $this->scheduleWriter = $scheduleWriter;
    }


    public function spreadSheetToArray(string $excelFile): array
    {
        $scheduleParser = new ScheduleParser();

        $schedule = $scheduleParser->parseScheduleXls($excelFile, ScheduleParser::createHospitalTimeSlices());

        return $schedule->toArray();
    }

    public function arrayToSpreadSheet(array $data, string $excelFile, string $originalFileContent = ''): void
    {
        $schedule = new Schedule($data);

        $originalFile = tempnam('/tmp', 'roster');
        file_put_contents($originalFile, $originalFileContent);

        $this->scheduleWriter->writeSchedule($originalFile, $schedule, $excelFile);
    }

    public function validateDataArray(array $data): void
    {
        // not implemented yet
        // TODO: Implement validateDataArray() method.
    }

}