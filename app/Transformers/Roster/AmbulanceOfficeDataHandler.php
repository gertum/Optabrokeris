<?php

namespace App\Transformers\Roster;

use App\Domain\Roster\Hospital\ScheduleParser;
use App\Domain\Roster\Hospital\ScheduleWriter;
use App\Domain\Roster\Schedule;
use App\Models\Job;
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
        // TODO DI parser too
        $scheduleParser = new ScheduleParser();

        $schedule = $scheduleParser->parseScheduleXls($excelFile, ScheduleParser::createHospitalTimeSlices());

        // fill missing skills because they are non-existent in the exel file.
        $schedule->fillSkills('medicine' );
        $schedule->fillLocation('ambulance office');

        return $schedule->toArray();
    }


    public function arrayToSpreadSheet(array $data, string $excelFile, ?Job $job): void
    {
        // TODO make choice here depending on how we want to output results
        $schedule = new Schedule($data);

        $originalFile = tempnam('/tmp', 'roster');
        file_put_contents($originalFile, $job->getOriginalFileContent());

        $this->scheduleWriter->writeSchedule($originalFile, $schedule, $excelFile);
    }

    public function validateDataArray(array $data): void
    {
        // not implemented yet
    }

}