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
                    ->setEnd($current->format(self::DT_FORMAT))
                ;
            }
        }
        return $shifts;
    }

    /**
     * @param float[] $shiftBounds hour in the day
     * @return Shift[]
     */
    public static function buildShiftsOfBounds(
        DateTimeImmutable $from,
        DateTimeImmutable $till,
        array $shiftBounds
    ): array {
        $timeSlices = self::transformBoundsToTimeSlices($shiftBounds);
        $initialInterval = self::getInitialInterval($shiftBounds[0]);
        $current = $from->add($initialInterval);

        return self::buildShifts($current, $till, $timeSlices);
    }

    /**
     * @param float[] $bounds each bound is an hour in a day;
     * @return DateInterval[]
     */
    public static function transformBoundsToTimeSlices(array $bounds): array
    {
        $cyclingBounds = array_merge($bounds, [$bounds[0] + 24]);

        $intervals = [];

        for ($i = 0; $i < count($cyclingBounds) - 1; $i++) {
            $currentBound = $cyclingBounds[$i];
            $nextBound = $cyclingBounds[$i + 1];
            $intervalFloat = $nextBound - $currentBound;
            $intervalHours = floor($intervalFloat);
            $intervalMinutes = round(($intervalFloat - $intervalHours) * 60);

            $intervals[] = new DateInterval(sprintf('PT%sH%sM', $intervalHours, $intervalMinutes));
        }

        return $intervals;
    }

    public static function getInitialInterval(float $firstBound): DateInterval
    {
        // we calculate from 00:00
        $intervalHours = floor($firstBound);
        $intervalMinutes = round(($firstBound - $intervalHours) * 60);

        return new DateInterval(sprintf('PT%sH%sM', $intervalHours, $intervalMinutes));
    }
}