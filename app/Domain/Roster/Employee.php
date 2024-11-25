<?php

namespace App\Domain\Roster;

use Spatie\DataTransferObject\DataTransferObject;

class Employee extends DataTransferObject
{
    public ?string $name;
    public ?array $skillSet;

    public float $maxWorkingHours = 0;

    /**
     * @deprecated use $row instead
     */
    private int $excelRow = 0;

    private int $row = 0;

    private int $sequenceNumber;


    /**
     * Darbo valandų per dieną.
     */
    private float $workingHoursPerDay = 8.0;

    /**
     * Etatų skaičius.
     */
    private float $positionAmount = 1.0;

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

    public function getMaxWorkingHours(): float
    {
        return $this->maxWorkingHours;
    }

    public function setMaxWorkingHours(float $maxWorkingHours): Employee
    {
        $this->maxWorkingHours = $maxWorkingHours;
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

    public function getWorkingHoursPerDayFormatted(): string
    {
        $hours = floor($this->workingHoursPerDay);
        $minutes = round(($this->workingHoursPerDay - floor($this->workingHoursPerDay)) * 60);

        return sprintf( "%d:%d", $hours, $minutes );
    }

    public function getPositionAmountFormatted(): string
    {
        return sprintf("%0.2f", $this->positionAmount);
    }

    /**
     * Should be used in future
     * @return mixed
     */
    public function getKey(): mixed
    {
        return $this->name;
    }

    public function getWorkingHoursPerDay(): float
    {
        return $this->workingHoursPerDay;
    }

    public function setWorkingHoursPerDay(float $workingHoursPerDay): Employee
    {
        $this->workingHoursPerDay = $workingHoursPerDay;

        return $this;
    }

    public function getPositionAmount(): float
    {
        return $this->positionAmount;
    }

    public function setPositionAmount(float $positionAmount): Employee
    {
        $this->positionAmount = $positionAmount;

        return $this;
    }
}