<?php

namespace App\Domain\Util;

interface HolidayProvider
{
    public function getHoliday(int $month, int $day) : ?string;
}