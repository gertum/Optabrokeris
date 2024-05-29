<?php

namespace App\Domain\Roster\Hospital;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Employee;
use Shuchkin\SimpleXLSX;

/**
 * The main excel parser class for now.
 */
class ExcelWrapper
{
    const MAX_ROWS = 70;
    const MAX_COLUMNS = 40;
    private array $rowsEx = [];
    private ?SimpleXLSX $xlsx = null;

    /**
     * In memory cache
     * @var Cell[][]
     */
    private array $cellCache = [];

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
            $employees [] = (new Employee())->setName($employeeCell->value)->setExcelRow($employeeCell->r);
        }

        return $employees;
    }


    /**
     * @return Availability[]
     */
    public function parseAvailabilities(): array
    {
        // TODO
        // relate cell row to relate with the parsed employees


        return [];
    }

    /**
     * @return Availability[]
     */
    public function parseAvailabilitiesForEilNr(EilNr $eilNr): array
    {
        /** @var Availability[] $availabilities */
        $availabilities = [];

        return $availabilities;
    }

    public function getShifts(): array
    {
        // TODO
        return [];
    }




}