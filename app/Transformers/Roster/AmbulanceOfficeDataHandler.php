<?php

namespace App\Transformers\Roster;

use App\Domain\Roster\Events\BeforeApplyingSubjectsToScheduleEvent;
use App\Domain\Roster\Hospital\DataFileDetector;
use App\Domain\Roster\Hospital\ScheduleParser;
use App\Domain\Roster\Hospital\ScheduleWriter;
use App\Domain\Roster\Hospital\ShiftsBuilder;
use App\Domain\Roster\Profile;
use App\Domain\Roster\Schedule;
use App\Exceptions\ExcelParseException;
use App\Exceptions\SolverDataException;
use App\Models\Job;
use App\Repositories\SubjectRepository;
use App\Transformers\SpreadSheetDataHandler;

class AmbulanceOfficeDataHandler implements SpreadSheetDataHandler
{
    private ScheduleWriter $scheduleWriter;
    private SubjectRepository $subjectRepository;
    private ScheduleParser $scheduleParser;
    private DataFileDetector $dataFileDetector;

    private string $templateFile = '';

    public function __construct(
        ScheduleWriter    $scheduleWriter,
        SubjectRepository $subjectRepository,
        ScheduleParser    $scheduleParser,
        DataFileDetector  $dataFileDetector
    )
    {
        $this->scheduleWriter = $scheduleWriter;
        $this->subjectRepository = $subjectRepository;
        $this->scheduleParser = $scheduleParser;
        $this->dataFileDetector = $dataFileDetector;
    }

    /**
     *
     */
    public function spreadSheetToArray(string $excelFile, ?Profile $profileObj = null): array
    {
        $type = $this->dataFileDetector->detectExcelType($excelFile);

        $schedule = null;
        $writeType = null;
        switch ($type) {
            case  DataFileDetector::TYPE_SCHEDULE_XLS:

                // default value
                $shiftBounds = [8, 20];

                // value from profile
                if ( $profileObj != null && count($profileObj->getShiftBounds()) > 0 ) {
                    $shiftBounds = $profileObj->getShiftBounds();
                }
//                $timeSlices = ShiftsBuilder::transformBoundsToTimeSlices($shiftBounds);

                // kol kas new nesuveikia, dar blogai ..
                $schedule = $this->scheduleParser->parseScheduleXlsNew(
                    $excelFile,
                    $shiftBounds
                )
                    ->fillSkills('medicine')
                    ->fillLocation('ambulance office');


                // These two functions should fix schedule to be more solvable by the solver.
//                $schedule->setShiftList($schedule->recalculateShiftsByBounds($profileObj->getShiftBounds()));
                $schedule->setAvailabilityList( $schedule->recalculateAvailabilitiesByShifts() );

                $writeType = Profile::WRITE_TYPE_ORIGINAL_FILE;
                break;
            case DataFileDetector::TYPE_AVAILABILITIES_XLS:
                $schedule = $this->scheduleParser->parsePreferredScheduleXls($excelFile, $profileObj);
                $employeesNames = $schedule->getEmployeesNames();
                $subjects = $this->subjectRepository->loadSubjectsByNames($employeesNames);
                BeforeApplyingSubjectsToScheduleEvent::dispatch($subjects, $schedule);
                $schedule->fillEmployeesWithSubjectsData($subjects);
                $writeType = Profile::WRITE_TYPE_TEMPLATE_FILE;
                break;
            case DataFileDetector::TYPE_SUBJECTS_XLS:
                throw new ExcelParseException('Given file best matches for subjects');
            case null:
                throw new ExcelParseException('Cant define file structure');
        }


        $array = $schedule->toArray();
        // TODO make this assignment not so hacky
        $array['writeType'] = $writeType;

        return $array;
    }


    public function arrayToSpreadSheet(array $data, string $excelFile, ?Job $job): void
    {
        $schedule = new Schedule($data);

        $subjects = $this->subjectRepository->loadSubjectsByNames($schedule->getEmployeesNames());

        BeforeApplyingSubjectsToScheduleEvent::dispatch($subjects, $schedule);
        $schedule->fillEmployeesWithSubjectsData($subjects);

        $profile = $job->getProfileObj();
        if ($profile->writeType == Profile::WRITE_TYPE_ORIGINAL_FILE) {
            $originalFile = tempnam('/tmp', 'roster');
            file_put_contents($originalFile, $job->getOriginalFileContent());

            $this->scheduleWriter->writeSchedule($originalFile, $schedule, $excelFile);
        } elseif ($profile->writeType == Profile::WRITE_TYPE_TEMPLATE_FILE) {
            $this->scheduleWriter->writeResultsUsingTemplate($schedule, $this->templateFile, $excelFile);
        } else {
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