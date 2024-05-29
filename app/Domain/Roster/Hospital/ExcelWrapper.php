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
    private array $rowsEx = [];
    private ?SimpleXLSX $xlsx = null;

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
        return new Cell($this->rowsEx[$row][$column]);
    }


    /**
     * @return Employee[]
     */
    public function parseEmployees() : array {
        // TODO later dynamically detect bounds
        $column = 1;
        $startRow = 9;
        $endRow = 60;

        $result = [];
        for ($row = $startRow; $row <= $endRow; $row++) {
            $cell = $this->getCell($row, $column);

            if ( $cell->value == '' ) {
                continue;
            }

            $employee = new Employee();
            $employee->setName( $cell->value);
            $employee->setExcelRow($cell->r); // difference between $cell->r - $row = 1
            // TODO skillSet
            $result[] =  $employee;
        }

        return $result;
    }

    /**
     * in memory cache
     * @var Employee[]|null
     */
    private ?array $employees=null;


    /**
     * @return Employee[]
     */
    public function getEmployees() : array {
        if ( $this->employees == null ) {
            $this->employees = $this->parseEmployees();
        }

        return $this->employees;
    }

    /**
     * @return Availability[]
     */
    public function getAvailabilities() : array {
        // TODO
        // relate cell row to relate with the parsed employees


        return [];
    }

    /**
     * @param Employee $employee
     * @return Availability[]
     */
    public function getAvailabilitiesForEmployee(Employee $employee) : array {
        $startColumn = 5;
        $endColumn = 40;

        return [];
    }

    public function getShifts() : array {
        // TODO
        return [];
    }
}