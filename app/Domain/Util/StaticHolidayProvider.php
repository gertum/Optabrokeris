<?php

namespace App\Domain\Util;

class StaticHolidayProvider implements HolidayProvider
{

    /**
     * Grouped by months
     * @var string[][]
     */
    private array $holidays = [];


    public function addHoliday(int $month, int $day, string $name) : self {
        $this->holidays[$month][$day] = $name;

        return $this;
    }

    public function getHoliday(int $month, int $day): ?string
    {

        if ( !array_key_exists($month, $this->holidays) ) {
            return null;
        }

        if ( !array_key_exists($day, $this->holidays[$month]) ) {
            return null;
        }

        return $this->holidays[$month][$day];
    }
}