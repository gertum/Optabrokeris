<?php

namespace App\Util;

use App\Domain\Util\HolidayProvider;
use Carbon\Carbon;
class WorkingDaysCalculator
{

    public static function calculateWorkingDaysInMonth(int $year, int $month, HolidayProvider $holidayProvider, array $weekWorkDays ) : int  {

        $dayInMonth = Carbon::create($year, $month, 1);

        $workingDaysCount = 0;

        while ( $dayInMonth->month == $month ) {
            $holiday = $holidayProvider->getHoliday($month, $dayInMonth->day);
            $weekDay = $dayInMonth->weekday();

            if ( $holiday == null && in_array(  $weekDay, $weekWorkDays)) {
                $workingDaysCount ++;
            }

            $dayInMonth->addDay();
        }

        return $workingDaysCount;
    }
}