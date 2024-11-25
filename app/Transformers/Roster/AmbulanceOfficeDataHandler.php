<?php

namespace App\Transformers\Roster;

use App\Domain\Roster\Hospital\ScheduleParser;
use App\Domain\Roster\Hospital\ScheduleWriter;
use App\Domain\Roster\Profile;
use App\Domain\Roster\Schedule;
use App\Exceptions\SolverDataException;
use App\Models\Job;
use App\Repositories\SubjectRepository;
use App\Transformers\SpreadSheetDataHandler;

class AmbulanceOfficeDataHandler implements SpreadSheetDataHandler
{

    private ScheduleWriter $scheduleWriter;
    private SubjectRepository $subjectRepository;

    private string $templateFile='';

    /**
     * @param ScheduleWriter $scheduleWriter
     */
    public function __construct(ScheduleWriter $scheduleWriter, SubjectRepository $subjectRepository)
    {
        $this->scheduleWriter = $scheduleWriter;
        $this->subjectRepository = $subjectRepository;
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
        $schedule = new Schedule($data);

        $subjects = $this->subjectRepository->loadSubjectsByNames( $schedule->getEmployeesNames() );
        $schedule->fillEmployeesWithSubjectsData($subjects);

        $profile = $job->getProfileObj();
        if ( $profile->writeType == Profile::WRITE_TYPE_ORIGINAL_FILE ) {

            $originalFile = tempnam('/tmp', 'roster');
            file_put_contents($originalFile, $job->getOriginalFileContent());

            $this->scheduleWriter->writeSchedule($originalFile, $schedule, $excelFile);
        }
        elseif ($profile->writeType == Profile::WRITE_TYPE_TEMPLATE_FILE) {
            $this->scheduleWriter->writeResultsUsingTemplate($schedule, $this->templateFile, $excelFile);
        }
        else {
            throw new SolverDataException(sprintf('Unknown write type %s', $profile->writeType));
        }
    }

    public function validateDataArray(array $data): void
    {
        // not implemented yet
    }

    public function setTemplateFile(string $templateFile): AmbulanceOfficeDataHandler
    {
        $this->templateFile = $templateFile;

        return $this;
    }
}