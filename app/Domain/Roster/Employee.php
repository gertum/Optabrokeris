<?php

namespace App\Domain\Roster;

use Spatie\DataTransferObject\DataTransferObject;

class Employee extends DataTransferObject
{
    public ?string $name;
    public ?array $skillSet;

    public float $hoursLimit=0;

    /**
     * @deprecated use $row instead
     */
    private int $excelRow=0;

    private int $row=0;

    private int $sequenceNumber;

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

    public function getHoursLimit(): float
    {
        return $this->hoursLimit;
    }

    public function setHoursLimit(float $hoursLimit): Employee
    {
        $this->hoursLimit = $hoursLimit;
        return $this;
    }

    public function getSequenceNumber(): int
    {
        return $this->sequenceNumber;
    }

    public function setSequenceNumber(int $sequenceNumber): Employee
    {
        $this->sequenceNumber = $sequenceNumber;
        return $this;
    }
}