<?php

namespace App\Domain\Roster\Hospital\Write;

use App\Domain\Roster\Employee;

class DayOccupation
{
    private int $day;
    private float $startHour;
    private float $endHour;

    private ?Employee $employee;

    public function getDay(): int
    {
        return $this->day;
    }

    public function setDay(int $day): DayOccupation
    {
        $this->day = $day;
        return $this;
    }

    public function getStartHour(): float
    {
        return $this->startHour;
    }

    public function setStartHour(float $startHour): DayOccupation
    {
        $this->startHour = $startHour;
        return $this;
    }

    public function getEndHour(): float
    {
        return $this->endHour;
    }

    public function setEndHour(float $endHour): DayOccupation
    {
        $this->endHour = $endHour;
        return $this;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): DayOccupation
    {
        $this->employee = $employee;
        return $this;
    }

    public function createGroupKey() : string {
        if ( $this->employee== null ) {
            return "__". $this->day;
        }
        return $this->employee->name . "__". $this->day;
    }
}