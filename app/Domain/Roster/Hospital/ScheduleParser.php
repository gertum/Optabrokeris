<?php

namespace App\Domain\Roster\Hospital;

use App\Data\Profile;
use App\Domain\Roster\Availability;
use App\Domain\Roster\Employee;
use App\Domain\Roster\Schedule;
use App\Exceptions\ExcelParseException;
use Carbon\Carbon;
use DateInterval;
use DateTimeInterface;

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

    public function parsePreferedScheduleXls(string $file, Profile $profile): Schedule
    {
        $schedule = new Schedule();


        $wrapper = PreferencesExcelWrapper::parse($file);


        // parse employees

        $employees = $this->parseEmployees(wrapper: $wrapper);
        $schedule->setEmployeeList($employees);

        // build shifts
        $dateRecognizer = $wrapper->findYearMonth();
        $dateFrom = Carbon::create($dateRecognizer->getYear(), $dateRecognizer->getMonth())->toImmutable();
        $dateTill = Carbon::create($dateRecognizer->getYear(), $dateRecognizer->getMonth(), $dateFrom->daysInMonth, 24)->toImmutable();
        $shifts = ShiftsBuilder::buildShiftsOfBounds($dateFrom, $dateTill, $profile->getShiftBounds());
        $schedule->setShiftList($shifts);

        $availabilities = $this->parseAvailabilities($wrapper);

        return $schedule;
    }

    public function parseEmployees(PreferencesExcelWrapper $wrapper): array
    {
        $row = 2;

        $employees = [];
        while (true) {
            $employeeCell = $wrapper->getCell($row, 0);
            if ($employeeCell->value == null) {
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

    /**
     * @param Employee[] $employees
     * @return Availability[]
     */
    public function parseAvailabilities(PreferencesExcelWrapper $wrapper, Carbon $startingDate, array $employees, Profile $profile): array
    {
        $availabilities = [];
        // 1) find a cell with value '1' at the row index 1, then use it as the marker of the column,
        // where availabilities starts from

        $headerRow = 1;
        $startingCell = null;
        for ($headerColumn = 1; $headerColumn <= 5; $headerColumn++) {
            $cell = $wrapper->getCell($headerRow, $headerColumn);
            if ($cell->value == '1') {
                $startingCell = $cell;
                break;
            }
        }

        if ($startingCell != null) {
            throw new ExcelParseException('When parsing excel can\'t find header with a day 1');
        }

        foreach ($employees as $employee) {
            for ($day = 1; $day <= $startingDate->daysInMonth; $day++) {
                $row = $employee->getRow();
                $column = $startingCell->column + $day;
                $availabilityCell = $wrapper->getCell($row, $column);
                $dayDate = Carbon::create($startingDate->year, $startingDate->month, $startingDate->day);
                $dayAvailabilities = $this->createAvailabilitiesForOneDay($availabilityCell->value, $profile, $dayDate, $employee);
                $availabilities = array_merge($availabilities, $dayAvailabilities);
            }
        }

        return $availabilities;
    }

    public static function createAvailabilitiesForOneDay(string $availabilityMarker, Profile $profile, DateTimeInterface $day, Employee $employee): array
    {
        if (in_array($availabilityMarker, ['X', 'x', 'a', 'A'])) {
            $availabilityType = Availability::UNAVAILABLE;
            // TODO mark all shifts in that day
        } elseif ($availabilityMarker == '') {
            $availabilityType = Availability::AVAILABLE;
            // TODO mark all shifts in that day
        } elseif (in_array($availabilityMarker, ['8-8', '8-8r.', 'P', 'p'])) {
            $availabilityType = Availability::DESIRED;
            // TODO mark all shifts in that day
        } elseif (in_array($availabilityMarker, ['D', 'd'])) {
            $availabilityType = Availability::DESIRED;
            // mark one part as desired , other as unavailable
        } elseif (in_array($availabilityMarker, ['N', 'n'])) {
            $availabilityType = Availability::DESIRED;
            // mark one part as desired , other as unavailable
        }

        return [];
    }
}