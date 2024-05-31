<?php

namespace App\Domain\Roster\Hospital;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Employee;
use App\Exceptions\ExcelParseException;
use App\Util\MapBuilder;
use Carbon\Carbon;
use Shuchkin\SimpleXLSX;

/**
 * The main excel parser class for now.
 */
class ExcelWrapper
{
    const MAX_ROWS = 70;
    const MAX_COLUMNS = 40;

    const UNAVAILABLE_BACGROUND = '#FF0000';

    private array $rowsEx = [];
    private ?SimpleXLSX $xlsx = null;

    /**
     * In memory cache
     * @var Cell[][]
     */
    private array $cellCache = [];

    private int $availabilityId=1;

    public static function parse(string $file): self
    {
        $wrapper = new ExcelWrapper();

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


    public function getCell($row, $column): Cell
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
        for ($row = 0; $row <= self::MAX_ROWS; $row++) {
            for ($column = 0; $column <= self::MAX_COLUMNS; $column++) {
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
        for ($row = $eilNrTitle->getRow() + $eilNrTitle->getRowSpan(); $row < self::MAX_ROWS; $row++) {
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

        foreach ($eilNrs as $eilNr) {
            $employeeRow = $eilNr->getRow();
            $employeeColumn = $eilNr->getColumn() + 1;

            $employeeCell = $this->getCell($employeeRow, $employeeColumn);
//            // TODO skillSet
            $employees [] = (new Employee())
                ->setName($employeeCell->value)
                ->setExcelRow($employeeCell->r)
                ->setRow($eilNr->getRow());
        }

        return $employees;
    }


    /**
     * @param EilNr[] $eilNrs
     * @param Employee[] $employees
     *
     * @return Availability[][]
     */
    public function parseAvailabilities(array $eilNrs, array $employees, int $year, int $month): array
    {
        $this->availabilityId = 0;
        /** @var Employee[] $employeesByRow */
        $employeesByRow = MapBuilder::buildMap($employees, fn(Employee $employee) => $employee->getRow());

        /** @var Availability[][] $availabilities */
        $availabilities = [];

        foreach ($eilNrs as $eilNr) {
            if (!array_key_exists($eilNr->getRow(), $employeesByRow)) {
                throw new ExcelParseException(sprintf('No employee in row %s', $eilNr->getRow()));
            }

            $employee = $employeesByRow[$eilNr->getRow()];
            $employeeAvailabilities = $this->parseAvailabilitiesForEilNr($eilNr, $year, $month, $employee);
            $availabilities[$eilNr->getValue()] = $employeeAvailabilities;
        }

        return $availabilities;
    }

    /**
     * @return Availability[]
     */
    public function parseAvailabilitiesForEilNr(EilNr $eilNr, int $year, int $month, Employee $employee): array
    {
        /** @var Availability[] $availabilities */
        $availabilities = [];

        $row = $eilNr->getRow();

        $monthDate = Carbon::create($year, $month);
        for ($day = 1; $day <= $monthDate->daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day);
            $column = $eilNr->getColumn() + $day + 4;


            $availabilityCeil = $this->getCell($row, $column);

            $availabilityType = Availability::DESIRED;

            if ($availabilityCeil->getBackgroundColor() == self::UNAVAILABLE_BACGROUND) {
                $availabilityType = Availability::UNAVAILABLE;
            }

            $availabilities[] = (new Availability())
                ->setId($this->availabilityId++)
                ->setEmployee($employee)
                ->setAvailabilityType($availabilityType)
                ->setDate($date);
        }

        return $availabilities;
    }

    public function getShifts(): array
    {
        // TODO
        return [];
    }


}