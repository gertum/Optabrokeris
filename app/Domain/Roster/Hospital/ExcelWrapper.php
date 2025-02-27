<?php

namespace App\Domain\Roster\Hospital;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Employee;
use App\Exceptions\ExcelParseException;
use App\Fixed\FixedSimpleXLSX;
use App\Util\DateRecognizer;
use App\Util\MapBuilder;
use Carbon\Carbon;
use DateTimeInterface;

/**
 * The main excel parser class for now.
 */
class ExcelWrapper
{
    const DISTANCE_BETWEEN_NO_AND_AVAILABILITIES = 4;

    const UNAVAILABLE_BACKGROUND = '#FF0000';
    const SEPARATOR_BACKGROUND = '#92D050';
    const UNAVAILABLE_BACKGROUND_UNHASHED = 'FF0000';
    const UNAVAILABLE_DAY_BACKGROUND_UNHASHED = 'FF8080';
    const UNAVAILABLE_NIGHT_BACKGROUND_UNHASHED = 'AA0000';

    const UNAVAILABLE_BACKGROUND_HASHED = '#FF0000';
    const UNAVAILABLE_DAY_BACKGROUND_HASHED = '#FF8080';
    const UNAVAILABLE_NIGHT_BACKGROUND_HASHED = '#AA0000';

    const WEEKEND_BACKGROUND_UNHASHED = 'BFBFBF';
    const SEPARATOR_BACKGROUND_UNHASHED = '92D050';

    const DESIRED_BACGROUND_UNHASHED = 'DDFFDD';
    const DESIRED_BACGROUND_HASHED = '#DDFFDD';

    const TARGET_DATE_FORMAT = 'Y-m-d\\TH:i:s';
//
    protected array $rowsEx = [];
    protected ?FixedSimpleXLSX $xlsx = null;

    /**
     * In memory cache
     * @var Cell[][]
     */
    protected array $cellCache = [];

    protected int $availabilityId = 1;

    /**
     * @var CellMatcherInterface[] array key is a custom name for a matcher.
     */
    protected array $registeredMatchers = [];

    protected static function getInstance(): static {
        return new static();
    }

    public static function parse(string $file): static
    {
        $wrapper = self::getInstance();

        if (!file_exists($file)) {
            throw new ExcelParseException(sprintf('File %s does not exist', $file));
        }

        $xlsx = FixedSimpleXLSX::parse($file);

        if ( !$xlsx ) {
            throw new ExcelParseException(sprintf('File %s is not a valid excel', $file));
        }

        $wrapper->xlsx = $xlsx;
        $wrapper->rowsEx = $wrapper->xlsx->rowsEx();

        return $wrapper;
    }

    public function getRowsEx(): array
    {
        return $this->rowsEx;
    }

    public function getXlsx(): ?FixedSimpleXLSX
    {
        return $this->xlsx;
    }


    public function getCell(int $row, int $column): Cell
    {
        if (!array_key_exists($row, $this->cellCache)) {
            $this->cellCache[$row] = [];
        }

        if (!array_key_exists($column, $this->cellCache[$row])) {
            if ( !isset($this->rowsEx[$row][$column])) {
                throw new ExcelParseException(sprintf('There is no cell at row %s and column %s', $row, $column));
            }

            $cell = new Cell($this->rowsEx[$row][$column]);
            $cell->row = $row;
            $cell->column = $column;
            $this->cellCache[$row][$column] = $cell;
        }

        return $this->cellCache[$row][$column];
    }


    // TODO use registered matchers
    public function findEilNrTitle(): ?EilNrTitle
    {
        for ($row = 0; $row < $this->getMaxRows(); $row++) {
            for ($column = 0; $column < $this->getMaxColumnsAtRow($row); $column++) {
                $cell = $this->getCell($row, $column);

                if (preg_match(EilNrTitle::EIL_NR_MARKER, $cell->value)) {
                    return (new EilNrTitle())->setRow($row)->setColumn($column);
                }
            }
        }

        return null;
    }

    /**
     * @return EilNr[]
     */
    public function parseEilNrs(EilNrTitle $eilNrTitle, $maxRowSpan = 2): array
    {
        /** @var EilNr[] $eilNrs */
        $eilNrs = [];

        $skippedEmpties = 0;
        for ($row = $eilNrTitle->getRow() + $eilNrTitle->getRowSpan(); $row < $this->getMaxRows(); $row++) {
            $cell = $this->getCell($row, $eilNrTitle->getColumn());

            if ($cell->value == '') {
                $skippedEmpties++;
                // break cycle when we encounter too much empty values
                if ($skippedEmpties == $maxRowSpan) {
                    break;
                }
                continue;
            }

            $skippedEmpties = 0;

            $eilNrs[] = (new EilNr())->setValue($cell->value)->setRow($row)->setColumn($eilNrTitle->getColumn());
        }
        return $eilNrs;
    }

    /**
     * @param EilNr[] $eilNrs
     * @return Employee[]
     */
    public function parseEmployees(array $eilNrs): array
    {
        $employees = [];

        $workingHoursTitle = $this->findWorkingHoursTitle();

        foreach ($eilNrs as $eilNr) {
            $employeeRow = $eilNr->getRow();
            $employeeColumn = $eilNr->getColumn() + 1;

            $employeeCell = $this->getCell($employeeRow, $employeeColumn);

            $workingHoursCell = $this->getCell($employeeRow, $workingHoursTitle->getColumn());
//            // TODO skillSet
            $employees [] = (new Employee())
                ->setName($employeeCell->value)
                ->setExcelRow($employeeCell->r)
                ->setRow($eilNr->getRow())
                ->setMaxWorkingHours(floatval($workingHoursCell->value))
                ->setSequenceNumber($eilNr->getValue());
        }

        return $employees;
    }


    /**
     * @param EilNr[] $eilNrs
     * @param Employee[] $employees
     *
     * @return Availability[][]
     */
    public function parseAvailabilities(
        array $eilNrs,
        array $employees,
        int $year,
        int $month,
        ?AvailableAssignmentConsumer $assignmentConsumer
    ): array {
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
            $employeeAvailabilities = $this->parseAvailabilitiesForEilNr(
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
    public function parseAvailabilitiesForEilNr(
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
            $column = $this->getColumnByDay($eilNr->getColumn(), $day);

            $availabilityCell = $this->getCell($row, $column);
            // go till green line ( add break in to the cycle )
            if ($availabilityCell->getBackgroundColor() == self::SEPARATOR_BACKGROUND) {
                break;
            }

            $availabilityType = Availability::AVAILABLE;

            if ($availabilityCell->getBackgroundColor() == self::UNAVAILABLE_BACKGROUND) {
                $availabilityType = Availability::UNAVAILABLE;
            }

            $availability = (new Availability())
                ->setId($this->availabilityId++)
                ->setEmployee($employee)
                ->setAvailabilityType($availabilityType)
                ->setDate($date);

            $from = $availabilityCell->value;
            $availabilityCell2 = $this->getCell($row + 1, $column);
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

                $fromDate = Carbon::parse($from)->setDate($year, $month, $day)->format(self::TARGET_DATE_FORMAT);
                $tillDate = Carbon::parse($till)->setDate($year, $month, $tillDay)->format(self::TARGET_DATE_FORMAT);

                if ($assignmentConsumer != null) {
                    $assignmentConsumer->setAssignment($fromDate, $tillDate, $employee);
                }
            }

            $availabilities[] = $availability;
        }

        return $availabilities;
    }


    // TODO use registered matchers
    public function findYearMonth(): DateRecognizer
    {
        $dateRecognizer = new DateRecognizer();

        for ($row = 0; $row < $this->getMaxRows(); $row++) {
            for ($column = 0; $column < $this->getMaxColumnsAtRow($row); $column++) {
                $cell = $this->getCell($row, $column);
                if ($dateRecognizer->recognizeMonthDate($cell->value)) {
                    return $dateRecognizer;
                }
            }
        }

        return $dateRecognizer;
    }

    // TODO use registered matchers
    public function findWorkingHoursTitle(): WorkingHoursTitleCellMatcher
    {
        $workingHoursTitle = new WorkingHoursTitleCellMatcher();

        for ($row = 0; $row < $this->getMaxRows(); $row++) {
            for ($column = 0; $column < $this->getMaxColumnsAtRow($row); $column++) {
                $cell = $this->getCell($row, $column);
                if ($workingHoursTitle->matchCell($cell, $row, $column)) {
                    return $workingHoursTitle;
                }
            }
        }


        return $workingHoursTitle;
    }

    public function findDaySumsTitle(): ?Cell
    {
        return $this->findCellWithValue('Dienos sumos:');
    }

    public function findWorkingHoursAssignedTitle(): ?Cell
    {
        return $this->findCellWithValue('Darbo valandų priskirta');
    }

    /**
     * @deprecated use cell matchers
     */
    public function findCellWithValue(string $cellValue): ?Cell
    {
        for ($row = 0; $row < $this->getMaxRows(); $row++) {
            for ($column = 0; $column < $this->getMaxColumnsAtRow($row); $column++) {
                $cell = $this->getCell($row, $column);
                if ($cellValue == $cell->value) {
                    return $cell;
                }
            }
        }
        return null;
    }

    /**
     * Detects greatest date by searching a separator column, or going max of this month days.
     */
    public function extractMaxAvailabilityDate(EilNr $eilNr, int $year, int $month): DateTimeInterface
    {
        $row = $eilNr->getRow();
        $monthDate = Carbon::create($year, $month);
        for ($day = 1; $day <= $monthDate->daysInMonth; $day++) {
            $column = $this->getColumnByDay($eilNr->getColumn(), $day);

            $availabilityCeil = $this->getCell($row, $column);
            // go till green line ( add break in to the cycle )
            if ($availabilityCeil->getBackgroundColor() == self::SEPARATOR_BACKGROUND) {
                // TODO if the green color is modified, better to check if the rgb part green is the greatest?
                return Carbon::create($year, $month, $day - 1);
            }
        }

        return Carbon::create($year, $month, $monthDate->daysInMonth);
    }

    public function getColumnByDay(int $eilNrColumn, int $day): int
    {
        return $eilNrColumn + $day + self::DISTANCE_BETWEEN_NO_AND_AVAILABILITIES;
    }

    public function getMaxRows(): int
    {
        return count($this->rowsEx);
    }

    public function getMaxColumnsAtRow(int $row): int
    {
        return count($this->rowsEx[$row]);
    }

    public function registerMatcher(  string $name, CellMatcherInterface $cellMatcher) : self {
        $this->registeredMatchers[$name] = $cellMatcher;

        return $this;
    }

    public function getMatcher(string $name) : ?CellMatcherInterface {
        return $this->registeredMatchers[$name] ?? null;
    }

    public function runMatchers() : void {
        for ($row = 0; $row < $this->getMaxRows(); $row++) {
            for ($column = 0; $column < $this->getMaxColumnsAtRow($row); $column++) {
                $cell = $this->getCell($row, $column);
                foreach ($this->registeredMatchers as $matcher) {
                    if ( $matcher->matchCell($cell, $row, $column) ) {
                        // assume that all matchers are exclusive to each other
                        break;
                    }
                }
            }
        }
    }
}