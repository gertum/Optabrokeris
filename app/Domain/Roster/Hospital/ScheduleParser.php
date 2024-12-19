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
    const SYMBOLS_TO_AVAILABILITIES_MAP = [
        [
            [
                'Х', // different encoding!
                'X',
                'x',
                'a',
                'A'
            ],
            [Availability::UNAVAILABLE, Availability::UNAVAILABLE]
        ],
        // we will use both lowercase on the tested data
        [['', 'dn', 'd2'], [Availability::AVAILABLE, Availability::AVAILABLE]],
        [['p'], [Availability::DESIRED, Availability::DESIRED]],
        [['d'], [Availability::UNDESIRED, Availability::DESIRED]],
        [['n'], [Availability::DESIRED, Availability::UNDESIRED]],
        [['xd'], [Availability::AVAILABLE, Availability::UNAVAILABLE]],
        [['xn'], [Availability::UNAVAILABLE, Availability::AVAILABLE]],
    ];

    const SYMBOLS_TO_AVAILABILITIES_MAP_NEW = [
        [
            [
                'Х', // different encoding!
                'X',
                'x',
                'a',
                'A'
            ],
            [Availability::UNAVAILABLE, Availability::UNAVAILABLE]
        ],
        // we will use both lowercase on the tested data
        [['', 'dn', 'd2'], [Availability::AVAILABLE, Availability::AVAILABLE]],
        [['p'], [Availability::DESIRED, Availability::DESIRED]],
        [['d'], [Availability::DESIRED, Availability::UNDESIRED]],
        [['n'], [Availability::UNDESIRED, Availability::DESIRED]],
        [['xd'], [Availability::UNAVAILABLE, Availability::AVAILABLE]],
        [['xn'], [Availability::AVAILABLE, Availability::UNAVAILABLE]],
    ];


    const AVAILABILITIES_MATCHERS_KEYS = [
        'x',
        'xn',
        'xd',
        'a',
        'p',
        'd',
        'n',
        'd2',
        'dn',
    ];

    const SUBJECTS_MATCHERS_CRITICAL_KEYS = ['etatas', 'workingHours'];

    const SCHEDULE_MATCHERS_CRITICAL_KEYS = [
        'eilNr',
        'monthDays',
        'nameAndLastname',
    ];

    private int $parserVersion = 1;

    private $availabilityId=0;

    /**
     * @param DateInterval[] $timeSlices
     */
    public function parseScheduleXls(string $file, ?array $timeSlices = null): Schedule
    {
        // TODO reikia padaryti apjungimą pagal teisingus slices ( 8, 20 ) vietoj (0,8,20)
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
     * @deprecated deprecated in a new version, where time bounds from profile should be used
     */
    public static function createHospitalTimeSlices(): array
    {
        return [
            new DateInterval('PT8H'),
            new DateInterval('PT12H'),
            new DateInterval('PT4H'),
        ];
    }

    public function parsePreferredScheduleXls(string $file, Profile $profile): Schedule
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
        )->toImmutable();
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
        $skippedLines = 0;
        $sequenceNumber = 1;
        while ($row < $wrapper->getMaxRows()) {
            $employeeCell = $wrapper->getCell($row, 0);
            if ($employeeCell->value == null) {
                $skippedLines++;
                if ($skippedLines > 2) {
                    break;
                }

                $row++;
                continue;
            }

            $skippedLines = 0;

            $employees [] = (new Employee())
                ->setName($employeeCell->value)
                ->setExcelRow($employeeCell->r)
                ->setRow($row)
                ->setSequenceNumber($sequenceNumber++);
            // max working hours are taken from subjects

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
        CarbonInterface         $startingDate,
        array                   $employees
    ): array
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

            if ($this->parserVersion == 1) {
                $employeeAvailabilities = $this->createAvailabilitiesForMultipleDay($collectedValues, $startingDate);
            } elseif ($this->parserVersion == 2) {
                $employeeAvailabilities = $this->createAvailabilitiesForMultipleDayNew($collectedValues, $startingDate);
            }


            array_walk($employeeAvailabilities, fn(Availability $a) => $a->setEmployee($employee));

            // We remove key indexes, because else the following merge is impossible.
            /** @var Availability[] $availabilities */
            $availabilities = array_merge($availabilities, array_values($employeeAvailabilities));
        }

        $availabilityId = 1;
//        array_walk($availabilities, fn(Availability $availability) => $availability->setId($availabilityId++));
        foreach ($availabilities as $availability) {
            $availability->setId($availabilityId++);
        }

        return $availabilities;
    }

    /**
     * @param string[] $collectedValues
     * @param Carbon $startingDate
     * @return Availability[]
     * @deprecated use createAvailabilitiesForMultipleDayNew
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
     * @param string[] $collectedValues
     * @param Carbon $startingDate
     * @return Availability[]
     */
    public function createAvailabilitiesForMultipleDayNew(array $collectedValues, Carbon $startingDate): array
    {
        if (count($collectedValues) == 0) {
            return [];
        }

        $availabilities = [];

        foreach ($collectedValues as $day => $value) {
            $dayDate = Carbon::create($startingDate->year, $startingDate->month, $day);
            $dayAvailabilities = $this->createAvailabilitiesForOneDayNew($value, $dayDate);

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
     * @deprecated use createAvailabilitiesForOneDayNew instead
     */
    public function createAvailabilitiesForOneDay(?string $availabilitySymbols, Carbon $currentDay): array
    {
        $availabilities = [];

        // function 'modify' is not immutable
        $previousDay = clone($currentDay);
        $previousDay = $previousDay->modify('-1 day');

        // function 'modify' is not immutable
        $nextDay = clone($currentDay);
        $nextDay = $nextDay->modify('+1 day');

        $nightStartStr = Carbon::create($previousDay->year, $previousDay->month, $previousDay->day, 20)
            ->format(Schedule::TARGET_DATE_FORMAT);
        $dayStartStr = Carbon::create($currentDay->year, $currentDay->month, $currentDay->day, 8)
            ->format(Schedule::TARGET_DATE_FORMAT);
        $dayEndStr = Carbon::create($currentDay->year, $currentDay->month, $currentDay->day, 20)
            ->format(Schedule::TARGET_DATE_FORMAT);
        $nextDayStartStr = Carbon::create($nextDay->year, $nextDay->month, $nextDay->day, 8)
            ->format(Schedule::TARGET_DATE_FORMAT);

        $availabilitySymbols = strtolower(trim($availabilitySymbols));

        if (in_array($availabilitySymbols, ['8-8', '8-8r.']) || str_contains(
                $availabilitySymbols,
                '08-08'
            )) { // special case!!!
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
        } else {
            foreach (self::SYMBOLS_TO_AVAILABILITIES_MAP as list($symbolsArray, list($availabilityNight, $availabilityDay))) {
                if (in_array($availabilitySymbols, $symbolsArray)) {
                    $availabilities = [
                        (new Availability())
                            ->setDate($nightStartStr)
                            ->setDateTill($dayStartStr)
                            ->setAvailabilityType($availabilityNight),

                        (new Availability())
                            ->setDate($dayStartStr)
                            ->setDateTill($dayEndStr)
                            ->setAvailabilityType($availabilityDay),
                    ];
                }
            }
        }

        return MapBuilder::buildMap(
            $availabilities,
            fn(Availability $a) => $a->date instanceof DateTimeInterface ?
                $a->date->format(Schedule::TARGET_DATE_FORMAT) : $a->date
        );
    }

    /**
     * We are going to set availabilities independent on profile.
     * @return Availability[]
     */
    public function createAvailabilitiesForOneDayNew(?string $availabilitySymbols, Carbon $currentDay): array
    {
        $availabilities = [];

        // function 'modify' is not immutable
        $previousDay = clone($currentDay);
        $previousDay = $previousDay->modify('-1 day');

        // function 'modify' is not immutable
        $nextDay = clone($currentDay);
        $nextDay = $nextDay->modify('+1 day');

//        $nightStartStr = Carbon::create($currentDay->year, $currentDay->month, $currentDay->day, 20)
//            ->format(Schedule::TARGET_DATE_FORMAT)
//        ;
        $dayStartStr = Carbon::create($currentDay->year, $currentDay->month, $currentDay->day, 8)
            ->format(Schedule::TARGET_DATE_FORMAT);
        $dayEndStr = Carbon::create($currentDay->year, $currentDay->month, $currentDay->day, 20)
            ->format(Schedule::TARGET_DATE_FORMAT);
        $nightStartStr = $dayEndStr;

        $nextDayStartStr = Carbon::create($nextDay->year, $nextDay->month, $nextDay->day, 8)
            ->format(Schedule::TARGET_DATE_FORMAT);

        $availabilitySymbols = strtolower(trim($availabilitySymbols));

        if (in_array($availabilitySymbols, ['8-8', '8-8r.']) || str_contains(
                $availabilitySymbols,
                '08-08'
            )) { // special case!!!
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
        } else {
            foreach (self::SYMBOLS_TO_AVAILABILITIES_MAP_NEW as list($symbolsArray, list($availabilityDay, $availabilityNight))) {
                if (in_array($availabilitySymbols, $symbolsArray)) {
                    $availabilities = [
                        (new Availability)
                            ->setDate($dayStartStr)
                            ->setDateTill($dayEndStr)
                            ->setAvailabilityType($availabilityDay),
                        (new Availability)
                            ->setDate($nightStartStr)
                            ->setDateTill($nextDayStartStr)
                            ->setAvailabilityType($availabilityNight),

                    ];
                }
            }
        }

        return MapBuilder::buildMap(
            $availabilities,
            fn(Availability $a) => $a->date instanceof DateTimeInterface ?
                $a->date->format(Schedule::TARGET_DATE_FORMAT) : $a->date
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
        array  $availabilities,
        string $defaultAvailabilityType
    ): array
    {
        $currentDate = clone $startDate;
        $interval12 = new DateInterval('PT12H');

        while ($currentDate < $endDate) {
            $currentDateStr = $currentDate->format(Schedule::TARGET_DATE_FORMAT);
            $currentDate = $currentDate->add($interval12);

            if (array_key_exists($currentDateStr, $availabilities)) {
                continue;
            }
            $nextDateStr = $currentDate->format(Schedule::TARGET_DATE_FORMAT);

            $availability = (new Availability())
                ->setDate($currentDateStr)
                ->setDateTill($nextDateStr)
                ->setAvailabilityType($defaultAvailabilityType);

            $availabilities[$currentDateStr] = $availability;
        }

        // need to sort
        usort($availabilities, fn(Availability $a, Availability $b) => $a->date <=> $b->date);

        return $availabilities;
    }

    public static function registerStandardMatchers(ExcelWrapper $wrapper)
    {
        $wrapper->registerMatcher('datePlaceholder', new CustomValueCellMatcher('/DATE_PLACEHOLDER/'));
        $wrapper->registerMatcher('eilNr', new CustomValueCellMatcher('/eil.\s*nr/'));
        $wrapper->registerMatcher('workingHoursPerDay', new CustomValueCellMatcher('/Darbo val.* .* dien.*/'));
        $wrapper->registerMatcher('positionAmount', new CustomValueCellMatcher('/Etat.* skai.*ius/'));
        $wrapper->registerMatcher('workingHoursPerMonth', new CustomValueCellMatcher('/Darbo valand.* per m.*nes.*/'));
        $wrapper->registerMatcher('monthDays', new CustomValueCellMatcher('/M.*nesio dienos/'));
        $wrapper->registerMatcher('assignedHours', new CustomValueCellMatcher('/Darbo valand.* priskirta/'));
        $wrapper->registerMatcher('nameAndLastname', new CustomValueCellMatcher('/Vardas.*pavard.*/'));
    }

    public static function registerSubjectMatchers(ExcelWrapper $wrapper)
    {
        $wrapper->registerMatcher('etatas', new CustomValueCellMatcher('/etatas/'));
        $wrapper->registerMatcher('workingHours', new CustomValueCellMatcher('/darbo valandos/'));
    }

    public static function registerAvailabilitiesMatchers(ExcelWrapper $wrapper)
    {
        $wrapper->registerMatcher('x', new CustomValueCellMatcher('/^x$/i'));
        $wrapper->registerMatcher('xn', new CustomValueCellMatcher('/^xn$/i'));
        $wrapper->registerMatcher('xd', new CustomValueCellMatcher('/^xd$/i'));
        $wrapper->registerMatcher('a', new CustomValueCellMatcher('/^a$/i'));
        $wrapper->registerMatcher('p', new CustomValueCellMatcher('/^p$/i'));
        $wrapper->registerMatcher('d', new CustomValueCellMatcher('/^d$/i'));
        $wrapper->registerMatcher('n', new CustomValueCellMatcher('/^n$/i'));
        $wrapper->registerMatcher('d2', new CustomValueCellMatcher('/^d2$/i'));
        $wrapper->registerMatcher('dn', new CustomValueCellMatcher('/^dn$/i'));
    }

    public function getParserVersion(): int
    {
        return $this->parserVersion;
    }

    public function setParserVersion(int $parserVersion): ScheduleParser
    {
        $this->parserVersion = $parserVersion;
        return $this;
    }

    /**
     * new version of parseScheduleXls
     */
    public function parseScheduleXlsNew(string $file, ?array $timeSlices = null) : Schedule {
        // TODO reikia padaryti apjungimą pagal teisingus slices ( 8, 20 ) vietoj (0,8,20)
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

        $availabilities = $this->parseColorAvailabilities(
            $wrapper,
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

    public function parseColorAvailabilities(
        ExcelWrapper $wrapper,
        array $eilNrs,
        array $employees,
        int $year,
        int $month,
        ?AvailableAssignmentConsumer $assignmentConsumer

    ) {
        $this->availabilityId = 1;
        /** @var Employee[] $employeesByRow */
        $employeesByRow = MapBuilder::buildMap($employees, fn(Employee $employee) => $employee->getRow());

        /** @var Availability[][] $availabilities */
        $availabilities = [];

        foreach ($eilNrs as $eilNr) {
            if (!array_key_exists($eilNr->getRow(), $employeesByRow)) {
                throw new ExcelParseException(sprintf('No employee in row %s', $eilNr->getRow()));
            }

            $employee = $employeesByRow[$eilNr->getRow()];
            $employeeAvailabilities = $this->parseColorAvailabilitiesForEilNr(
                $wrapper,
                $eilNr,
                $year,
                $month,
                $employee,
                $assignmentConsumer
            );
            $availabilities[$eilNr->getValue()] = $employeeAvailabilities;
        }

        return $availabilities;
    }


    /**
     * @return Availability[]
     */
    public function parseColorAvailabilitiesForEilNr(
        ExcelWrapper $wrapper,
        EilNr $eilNr,
        int $year,
        int $month,
        Employee $employee,
        ?AvailableAssignmentConsumer $assignmentConsumer
    ): array {
        /** @var Availability[] $availabilities */
        $availabilities = [];

        $row = $eilNr->getRow();

        $monthDate = Carbon::create($year, $month);
        for ($day = 1; $day <= $monthDate->daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day);
            $column = $wrapper->getColumnByDay($eilNr->getColumn(), $day);

            $availabilityCell = $wrapper->getCell($row, $column);
            // go till green line ( add break in to the cycle )
            if ($availabilityCell->getBackgroundColor() == ExcelWrapper::SEPARATOR_BACKGROUND) {
                break;
            }

            $availabilityType = Availability::AVAILABLE;

            if ($availabilityCell->getBackgroundColor() == ExcelWrapper::UNAVAILABLE_BACKGROUND) {
                $availabilityType = Availability::UNAVAILABLE;
            }

            $availability = (new Availability())
                ->setId($this->availabilityId++)
                ->setEmployee($employee)
                ->setAvailabilityType($availabilityType)
                ->setDate($date);

            $from = $availabilityCell->value;
            $availabilityCell2 = $wrapper->getCell($row + 1, $column);
            $till = $availabilityCell2->value;

            if ($from != null || $till != null) {
                // DO not change availability type, because it  is not correct to make DESIRED for a whole day.
                // The DESIRED availability is set in the roster solver for the exact same time period as is defined in  the assigned shift.
//                $availability->setAvailabilityType(Availability::DESIRED);

                if ($from == null) {
                    $from = "00:00";
                }

                $tillDay = $day;
                if ($till == null) {
                    $till = "00:00";
                    $tillDay = $day + 1;
                }

                $fromDate = Carbon::parse($from)->setDate($year, $month, $day)->format(ExcelWrapper::TARGET_DATE_FORMAT);
                $tillDate = Carbon::parse($till)->setDate($year, $month, $tillDay)->format(ExcelWrapper::TARGET_DATE_FORMAT);

                if ($assignmentConsumer != null) {
                    $assignmentConsumer->setAssignment($fromDate, $tillDate, $employee);
                }
            }

            $availabilities[] = $availability;
        }

        return $availabilities;
    }
}