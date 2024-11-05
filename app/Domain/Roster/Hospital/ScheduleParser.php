<?php

namespace App\Domain\Roster\Hospital;

use App\Data\Profile;
use App\Domain\Roster\Employee;
use App\Domain\Roster\Schedule;
use Carbon\Carbon;
use DateInterval;

class ScheduleParser
{

    /**
     * @param DateInterval[] $timeSlices
     */
    public function parseScheduleXls(string $file, ?array $timeSlices = null): Schedule
    {
        if ($timeSlices == null || count($timeSlices) == 0) {
            $timeSlices = ScheduleParser::createHospitalTimeSlices();
        }

        $schedule = new Schedule();

        $wrapper = ExcelWrapper::parse($file);

        $eilNrTitle = $wrapper->findEilNrTitle();
        $eilNrs = $wrapper->parseEilNrs($eilNrTitle);
        $employees = $wrapper->parseEmployees($eilNrs);

        $dateRecognizer = $wrapper->findYearMonth();

        $dateFrom = Carbon::create($dateRecognizer->getYear(), $dateRecognizer->getMonth())->toImmutable();

        $maxAvailabilityDate = $wrapper->extractMaxAvailabilityDate(
            $eilNrs[0],
            $dateRecognizer->getYear(),
            $dateRecognizer->getMonth()
        );

        /** @var Carbon $dateTill */
        $dateTill = Carbon::createFromInterface($maxAvailabilityDate);
        $dateTill->setTime(24, 0);

        $shifts = ShiftsBuilder::buildShifts($dateFrom, $dateTill->toImmutable(), $timeSlices);

        $schedule->setShiftList($shifts);

        $assignmentConsumer = new ShiftsAvailableAssignmentConsumer($shifts);

        $availabilities = $wrapper->parseAvailabilities(
            $eilNrs,
            $employees,
            $dateRecognizer->getYear(),
            $dateRecognizer->getMonth(),
            $assignmentConsumer
        );

        $availabilitiesFlat = array_reduce(
            $availabilities,
            fn($flatList, $availabilitiesSubList) => array_merge($flatList, $availabilitiesSubList),
            []
        );

        $schedule->setEmployeeList($employees);
        $schedule->setAvailabilityList($availabilitiesFlat);

        return $schedule;
    }

    /**
     * @return DateInterval[]
     */
    public static function createHospitalTimeSlices(): array
    {
        return [
            new DateInterval('PT8H'),
            new DateInterval('PT12H'),
            new DateInterval('PT4H'),
        ];
    }

    public function parsePreferedScheduleXls(string $file, Profile $profile) : Schedule {
        $schedule = new Schedule();


        $wrapper = PreferencesExcelWrapper::parse($file);

        $yearMonth = $wrapper->findYearMonth();

        // TODO
        // parse employees

        $employees = $this->parseEmployees(wrapper: $wrapper );
        $schedule->setEmployeeList($employees);

        // TODO parse shifts

        return $schedule;
    }

    public function parseEmployees(PreferencesExcelWrapper $wrapper): array {
        $row = 2;

        $employees = [];
        while ( true ) {
            $employeeCell = $wrapper->getCell($row, 0);
            if ( $employeeCell->value == null ) {
                break;
            }

            $employees [] = (new Employee())
                ->setName($employeeCell->value)
                ->setExcelRow($employeeCell->r)
                ->setRow($row);
//                ->setMaxWorkingHours(floatval($workingHoursCell->value))
//                ->setSequenceNumber($eilNr->getValue());

            // TODO max working hours later

            $row++;
        }

        return $employees;

    }
}