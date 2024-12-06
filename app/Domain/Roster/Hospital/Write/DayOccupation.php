<?php

namespace App\Domain\Roster\Hospital\Write;

use App\Domain\Roster\Employee;
use App\Util\DateRecognizer;
use Carbon\Carbon;

class DayOccupation
{

    private int $day;
    private float $startHour;
    private float $endHour;

    private ?Carbon $startTime = null;
    private ?Carbon $endTime = null;

    private ?string $dateFormatted = null;

    private ?Employee $employee;

//    private float $occupiedHours;

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

    public function createGroupKey(): string
    {
        if ($this->employee == null) {
            return "__" . $this->dateFormatted;
        }
        return $this->employee->name . "__" . $this->dateFormatted;
    }

    public function getDateFormatted(): ?string
    {
        return $this->dateFormatted;
    }

    public function setDateFormatted(string $dateFormatted): DayOccupation
    {
        $this->dateFormatted = $dateFormatted;
        return $this;
    }

    public function getStartTime(): ?Carbon
    {
        return $this->startTime;
    }

    public function setStartTime(Carbon $startTime): DayOccupation
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): ?Carbon
    {
        return $this->endTime;
    }

    public function setEndTime(Carbon $endTime): DayOccupation
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function fixEndHour(): self
    {
        if ($this->endHour == 0) {
            $this->endHour = 24;
        }

        return $this;
    }

    public function calculateStartHour(): self
    {
        $this->startHour = DateRecognizer::calculateFloatingHourOfDate($this->startTime);

        return $this;
    }

    public function calculateEndHour(): self
    {
        $this->endHour = DateRecognizer::calculateFloatingHourOfDate($this->endTime);

        return $this;
    }

    public function clearIntermediateFields(): self
    {
        $this->startTime = null;
        $this->endTime = null;
        $this->dateFormatted = null;

        return $this;
    }

//    public function getOccupiedHours(): float
//    {
//        return $this->occupiedHours;
//    }
//
//    public function setOccupiedHours(float $occupiedHours): DayOccupation
//    {
//        $this->occupiedHours = $occupiedHours;
//        return $this;
//    }
}