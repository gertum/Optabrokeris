<?php

namespace App\Domain\Roster\Report;

use DateTime;

class DayInfo
{


    private int $day;

    private float $hoursTotal;

    public function __construct(int $day, float $hoursTotal)
    {
        $this->day = $day;
        $this->hoursTotal = $hoursTotal;
    }

    public function getHoursTotal(): float
    {
        return $this->hoursTotal;
    }

    public function setHoursTotal(float $hoursTotal): DayInfo
    {
        $this->hoursTotal = $hoursTotal;
        return $this;
    }

    public function getDay(): int
    {
        return $this->day;
    }

    public function setDay(int $day): DayInfo
    {
        $this->day = $day;
        return $this;
    }

    public function addHours(float $hours) {
        $this->hoursTotal += $hours;
    }
}