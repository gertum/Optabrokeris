<?php

namespace App\Domain\Roster\Hospital;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Employee;
use App\Exceptions\ExcelParseException;
use App\Util\DateRecognizer;
use App\Util\MapBuilder;
use Carbon\Carbon;
use DateTimeInterface;
use Shuchkin\SimpleXLSX;

/**
 * The main excel parser class for now.
 */
class ExcelWrapper
{
//    const MAX_ROWS = 70;
//    const MAX_COLUMNS = 40;

    const DISTANCE_BETWEEN_NO_AND_AVAILABILITIES = 4;

    const UNAVAILABLE_BACGROUND = '#FF0000';
    const SEPARATOR_BACKGROUND = '#92D050';

    const TARGET_DATE_FORMAT = 'Y-m-d\\TH:i:s';

    private array $rowsEx = [];
    private ?SimpleXLSX $xlsx = null;

    /**
     * In memory cache
     * @var Cell[][]
     */
    private array $cellCache = [];

    private int $availabilityId = 1;

    public static function parse(string $file): self
    {
        $wrapper = new ExcelWrapper();

        if (!file_exists($file)) {
            throw new ExcelParseException(sprintf('File %s does not exist', $file));
        }

        $wrapper->xlsx = SimpleXLSX::parse($file);
        $wrapper->rowsEx = $wrapper->xlsx->rowsEx();

        return $wrapper;
    }

    public function getRowsEx(): array
    {
        return $this->rowsEx;
    }

    public function getXlsx(): ?SimpleXLSX
    {
        return $this->xlsx;
    }


    public function getCell(int $row, int $column): Cell
    {
        if (!array_key_exists($row, $this->cellCache)) {
            $this->cellCache[$row] = [];
        }

        if (!array_key_exists($column, $this->cellCache[$row])) {
            $this->cellCache[$row][$column] = new Cell($this->rowsEx[$row][$column]);
        }

        return $this->cellCache[$row][$column];
    }


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

            $availabilityType = Availability::DESIRED;

            if ($availabilityCell->getBackgroundColor() == self::UNAVAILABLE_BACGROUND) {
                $availabilityType = Availability::UNAVAILABLE;
            }

            $availabilities[] = (new Availability())
                ->setId($this->availabilityId++)
                ->setEmployee($employee)
                ->setAvailabilityType($availabilityType)
                ->setDate($date);

            if ($assignmentConsumer != null) {
                $from = $availabilityCell->value;
                $availabilityCell2 = $this->getCell($row + 1, $column);
                $till = $availabilityCell2->value;

                if ($from != null || $till != null ) {
                    if ( $from == null ) {
                        $from = "00:00";
                    }
                    if ( $till == null ) {
                        $till = "00:00";
                    }

                    $from = Carbon::parse($from)->setDate($year, $month, $day)->format(self::TARGET_DATE_FORMAT);
                    $till = Carbon::parse($till)->setDate($year, $month, $day)->format(self::TARGET_DATE_FORMAT);

                    $assignmentConsumer->setAssignment($from, $till, $employee);
                }
            }
        }

        return $availabilities;
    }


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

    public function findWorkingHoursTitle(): WorkingHoursTitle
    {
        $workingHoursTitle = new WorkingHoursTitle();

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

    public function getMaxRows() : int {
        return count($this->rowsEx);
    }

    public function getMaxColumnsAtRow(int $row) : int {
        return count($this->rowsEx[$row]);
    }
}