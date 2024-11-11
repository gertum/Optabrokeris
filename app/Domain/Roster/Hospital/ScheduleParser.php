<?php

namespace App\Domain\Roster\Hospital;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Employee;
use App\Domain\Roster\Profile;
use App\Domain\Roster\Schedule;
use App\Exceptions\ExcelParseException;
use App\Util\MapBuilder;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use DateInterval;
use DateTimeInterface;

class ScheduleParser
{

    /**
     * @var string
     * @deprecated use Schedule::TARGET_DATE_FORMAT instead
     */
    const TARGET_DATE_FORMAT = 'Y-m-d\\TH:i:s';

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
        $dateTill = Carbon::create(
            $dateRecognizer->getYear(),
            $dateRecognizer->getMonth(),
            $dateFrom->daysInMonth,
            24
        )->toImmutable()
        ;
        $shifts = ShiftsBuilder::buildShiftsOfBounds($dateFrom, $dateTill, $profile->getShiftBounds());
        $schedule->setShiftList($shifts);

        $availabilities = $this->parseAvailabilities($wrapper, $dateFrom, $employees);
        $schedule->setAvailabilityList($availabilities);

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
                ->setRow($row)
            ;
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
    public function parseAvailabilities(
        PreferencesExcelWrapper $wrapper,
        CarbonInterface $startingDate,
        array $employees
    ): array {
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

        if ($startingCell == null) {
            throw new ExcelParseException('When parsing excel can\'t find header with a day 1');
        }

        foreach ($employees as $employee) {
            $collectedValues = [];
            for ($day = 1; $day <= $startingDate->daysInMonth; $day++) {
                $row = $employee->getRow();
                $column = $startingCell->column + $day - 1;

                if ($column > $wrapper->getMaxColumnsAtRow($row)) {
                    continue;
                }
                $availabilityCell = $wrapper->getCell($row, $column);
                $collectedValues[$day] = $availabilityCell->value;
            }

            $startingDate = Carbon::create($startingDate->year, $startingDate->month, 1);
            $employeeAvailabilities = $this->createAvailabilitiesForMultipleDay($collectedValues, $startingDate);


            array_walk($employeeAvailabilities, fn(Availability $a) => $a->setEmployee($employee));

            // We remove key indexes, because else the following merge is impossible.
            $availabilities = array_merge($availabilities, array_values($employeeAvailabilities));
        }

        $availabilityId = 1;
        array_walk($availabilities, fn(Availability $availability) => $availability->setId($availabilityId++));

        return $availabilities;
    }

    /**
     * @param string[] $collectedValues
     * @param Carbon $startingDate
     * @return Availability[]
     */
    public function createAvailabilitiesForMultipleDay(array $collectedValues, Carbon $startingDate): array
    {
        if (count($collectedValues) == 0) {
            return [];
        }

        $availabilities = [];

        foreach ($collectedValues as $day => $value) {
            $dayDate = Carbon::create($startingDate->year, $startingDate->month, $day);
            $dayAvailabilities = $this->createAvailabilitiesForOneDay($value, $dayDate);

            // we may resolve overlapping issues by indexing availabilities by availability start date and then check dates when merging arrays.
            $availabilities = self::mergeAvailabilities($availabilities, $dayAvailabilities);
        }

        // --- filling gaps
        $maxDay = max(array_keys($collectedValues));
        $minDay = min(array_keys($collectedValues));

        $startingDateTime = Carbon::create($startingDate->year, $startingDate->month, $minDay);
        $startingDateTime = $startingDateTime->modify('-1 day');
        $startingDateTime->setTime(20, 0);


        $endDateTime = Carbon::create($startingDate->year, $startingDate->month, $maxDay);
        $endDateTime = $endDateTime->modify('+1 day');
        $endDateTime->setTime(8, 0); // we take next day morning
        $availabilities = $this->fillGaps($startingDateTime, $endDateTime, $availabilities, Availability::UNDESIRED);
        // ---

        return $availabilities;
    }

    /**
     * We are going to set availabilities independent on profile.
     * @return Availability[]
     */
    public function createAvailabilitiesForOneDay(?string $availabilityValue, Carbon $currentDay): array
    {
        $availabilities = [];

        // function 'modify' is not immutable
        $previousDay = clone($currentDay);
        $previousDay = $previousDay->modify('-1 day');

        // function 'modify' is not immutable
        $nextDay = clone($currentDay);
        $nextDay = $nextDay->modify('+1 day');

        // TODO take hours from settings
        $nightStartStr = Carbon::create($previousDay->year, $previousDay->month, $previousDay->day, 20)
            ->format(self::TARGET_DATE_FORMAT)
        ;
        $dayStartStr = Carbon::create($currentDay->year, $currentDay->month, $currentDay->day, 8)
            ->format(self::TARGET_DATE_FORMAT)
        ;
        $dayEndStr = Carbon::create($currentDay->year, $currentDay->month, $currentDay->day, 20)
            ->format(self::TARGET_DATE_FORMAT)
        ;
        $nextDayStartStr = Carbon::create($nextDay->year, $nextDay->month, $nextDay->day, 8)
            ->format(self::TARGET_DATE_FORMAT)
        ;

        $availabilityValue = trim($availabilityValue);

        // be aware: these two X are not the same!!
        if (in_array(
            $availabilityValue,
            [
                'Х', // different encoding!
                'X',
                'x',
                'a',
                'A'
            ]
        )) {
            $availabilities = [
                (new Availability())
                    ->setDate($nightStartStr)
                    ->setDateTill($dayStartStr)
                    ->setAvailabilityType(Availability::UNAVAILABLE),

                (new Availability())
                    ->setDate($dayStartStr)
                    ->setDateTill($dayEndStr)
                    ->setAvailabilityType(Availability::UNAVAILABLE)
            ];
        } elseif ($availabilityValue == '') {
            $availabilities = [
                (new Availability())
                    ->setDate($nightStartStr)
                    ->setDateTill($dayStartStr)
                    ->setAvailabilityType(Availability::AVAILABLE),

                (new Availability())
                    ->setDate($dayStartStr)
                    ->setDateTill($dayEndStr)
                    ->setAvailabilityType(Availability::AVAILABLE)
            ];
        } elseif (in_array($availabilityValue, ['8-8', '8-8r.']) || str_contains($availabilityValue, '08-08')) {
            $availabilities = [
                (new Availability())
                    ->setDate($dayStartStr)
                    ->setDateTill($dayEndStr)
                    ->setAvailabilityType(Availability::DESIRED),

                (new Availability())
                    ->setDate($dayEndStr)
                    ->setDateTill($nextDayStartStr)
                    ->setAvailabilityType(Availability::DESIRED)
            ];
        } elseif (in_array($availabilityValue, ['P', 'p'])) {
            $availabilities = [
                (new Availability())
                    ->setDate($nightStartStr)
                    ->setDateTill($dayStartStr)
                    ->setAvailabilityType(Availability::DESIRED),

                (new Availability())
                    ->setDate($dayStartStr)
                    ->setDateTill($dayEndStr)
                    ->setAvailabilityType(Availability::DESIRED)
            ];
        } elseif (in_array($availabilityValue, ['D', 'd'])) {
            $availabilities = [
                (new Availability())
                    ->setDate($nightStartStr)
                    ->setDateTill($dayStartStr)
                    ->setAvailabilityType(Availability::UNDESIRED),

                (new Availability())
                    ->setDate($dayStartStr)
                    ->setDateTill($dayEndStr)
                    ->setAvailabilityType(Availability::DESIRED)
            ];
        } elseif (in_array($availabilityValue, ['N', 'n'])) {
            $availabilities = [
                (new Availability())
                    ->setDate($nightStartStr)
                    ->setDateTill($dayStartStr)
                    ->setAvailabilityType(Availability::DESIRED),

                (new Availability())
                    ->setDate($dayStartStr)
                    ->setDateTill($dayEndStr)
                    ->setAvailabilityType(Availability::UNDESIRED)
            ];
        } else {
            throw new ExcelParseException(sprintf('Unknown value [%s] to parse', $availabilityValue));
        }

        return MapBuilder::buildMap(
            $availabilities,
            fn(Availability $a) => $a->date instanceof DateTimeInterface ?
                $a->date->format(self::TARGET_DATE_FORMAT) : $a->date
        );
    }

    /**
     * @param Availability[] $a
     * @param Availability[] $b
     * @return Availability[]
     */
    public function mergeAvailabilities(array $a, array $b): array
    {
        $result = $a;

        foreach ($b as $day => $bAvailability) {
            if (!array_key_exists($day, $result)) {
                $result[$day] = $bAvailability;
                continue;
            }

            // overlapping
            $aAvailability = $a[$day];

            $aPriority = Availability::getAvailabilityTypePriority($aAvailability->availabilityType);
            $bPriority = Availability::getAvailabilityTypePriority($bAvailability->availabilityType);

            $selectedAvailability = $aAvailability;
            if ($bPriority > $aPriority) {
                $selectedAvailability = $bAvailability;
            }

            $result[$day] = $selectedAvailability;
        }

        return $result;
    }

    public function fillGaps(
        Carbon $startDate,
        Carbon $endDate,
        array $availabilities,
        string $defaultAvailabilityType
    ): array {
        $currentDate = clone $startDate;
        $interval12 = new DateInterval('PT12H');

        while ($currentDate < $endDate) {
            $currentDateStr = $currentDate->format(self::TARGET_DATE_FORMAT);
            $currentDate = $currentDate->add($interval12);

            if (array_key_exists($currentDateStr, $availabilities)) {
                continue;
            }
            $nextDateStr = $currentDate->format(self::TARGET_DATE_FORMAT);

            $availability = (new Availability())
                ->setDate($currentDateStr)
                ->setDateTill($nextDateStr)
                ->setAvailabilityType($defaultAvailabilityType)
            ;

            $availabilities[$currentDateStr] = $availability;
        }

        // need to sort
        usort($availabilities, fn(Availability $a, Availability $b) => $a->date <=> $b->date);

        return $availabilities;
    }
}