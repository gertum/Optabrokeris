<?php

namespace App\Domain\Roster;

use Spatie\DataTransferObject\DataTransferObject;

class Employee extends DataTransferObject
{
    public ?string $name;
    public ?array $skillSet;

    /**
     * @deprecated use $row instead
     */
    private int $excelRow=0;

    private int $row=0;

    public function setName(string $name): Employee
    {
        $this->name = $name;
        return $this;
    }

    public function setSkillSet(array $skillSet): Employee
    {
        $this->skillSet = $skillSet;
        return $this;
    }


    public function getExcelRow(): int
    {
        return $this->excelRow;
    }

    public function setExcelRow(int $excelRow): Employee
    {
        $this->excelRow = $excelRow;
        return $this;
    }

    public function getRow(): int
    {
        return $this->row;
    }

    public function setRow(int $row): Employee
    {
        $this->row = $row;
        return $this;
    }
}