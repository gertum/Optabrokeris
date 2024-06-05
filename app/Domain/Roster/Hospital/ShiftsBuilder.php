<?php

namespace App\Domain\Roster\Hospital;

use App\Domain\Roster\Shift;
use DateInterval;
use DateTimeImmutable;

class ShiftsBuilder
{
    const DT_FORMAT = 'Y-m-d\TH:i:s';

    /**
     * @param DateInterval [] $timeSlices marks time intervals for one day
     * @return Shift[]
     */
    public static function buildShifts(DateTimeImmutable $from, DateTimeImmutable $till, array $timeSlices): array
    {
        $shiftId = 1;
        $current = $from;
        $shifts = [];
        while ($current < $till) {
            foreach ($timeSlices as $timeSlice) {
                $previous = $current;
                $current = $previous->add($timeSlice);

                $shifts[] = (new Shift())
                    ->setId($shiftId++)
                    ->setStart($previous->format(self::DT_FORMAT))
                    ->setEnd($current->format(self::DT_FORMAT));
            }
        }
        return $shifts;
    }
}