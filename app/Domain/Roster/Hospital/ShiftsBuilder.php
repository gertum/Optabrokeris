<?php

namespace App\Domain\Roster\Hospital;

use App\Domain\Roster\Shift;
use Carbon\Carbon;
use DateTimeInterface;
use DateInterval;

class ShiftsBuilder
{
    /**
     * @param DateInterval [] $timeSlices marks time intervals for one day
     * @return Shift[]
     */
    public static function buildShifts(array $timeSlices, Carbon $from, Carbon $till) : array {
        $current = clone $from;

        $result = [];


        while ( $current < $till) {
            foreach ($timeSlices as $timeSlice) {
                // check if we should clone $current date
                $next = $current->add($timeSlice);
            }
        }

        return $result;
    }
}