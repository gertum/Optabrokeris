<?php

namespace App\Domain\Roster\Hospital\Write;

use App\Domain\Roster\Shift;
use App\Util\Grouper;
use Carbon\Carbon;

/**
 * This class is used to transform shifts list to a table-type structure
 * for writing data to the output xlsx file.
 */
class ShiftsListTransformer
{
    const EPSILON = 0.0001;
    /**
     * @param Shift[] $shifts
     * @return DayOccupation[]
     */
    public static function transform(array $shifts): array
    {
        /** @var DayOccupation[] $occupations */
        $occupations = [];

        foreach ($shifts as $shift) {
            $dayOccupation = new DayOccupation();

            $startDate = Carbon::create($shift->start);
            $endDate = Carbon::create($shift->end);

            $dayOccupation->setEmployee($shift->employee);
            $dayOccupation->setDay($startDate->day);
            $dayOccupation->setStartHour($startDate->hour + $startDate->minute / 60);

            // if hour is in another day, add 24 hour to it

            $addition = ($endDate->day - $startDate->day) * 24;

            $dayOccupation->setEndHour( $addition +$endDate->hour + $endDate->minute / 60 );

            $occupations[] = $dayOccupation;
        }

        // lets sort
        usort($occupations, fn(DayOccupation $a, DayOccupation $b) =>
            ($a->getDay() <=> $b->getDay()) * 2 +
            ($a->getStartHour() <=> $b->getStartHour())
        );


        // join occupations

        /** @var DayOccupation[][] $groupedOccupations */
        $groupedOccupations = Grouper::group($occupations, fn(DayOccupation $o) => $o->createGroupKey());


        /** @var DayOccupation[] $resultOccupations */
        $resultOccupations = [];

        foreach ($groupedOccupations as $oGroup) {

            // still need to split between two days
//            if (count($oGroup) == 1) {
//                $resultOccupations[] = current($oGroup);
//                continue;
//            }

            // > 1

            $startHour = $oGroup[0]->getStartHour();
            $startDay = $oGroup[0]->getDay();
            $endDay = last($oGroup)->getDay();
            $endHour = last($oGroup)->getEndHour();

            if ( $endHour > 24 ) {
                $endDay += floor($endHour / 24);
                $endHour = $endHour % 24;
            }

            // gap detecting block
            $gap = 0;
            for ( $i = 1; $i < count($oGroup); $i++ ) {
                $previousOccupation = $oGroup[$i-1];
                $currentOccupation = $oGroup[$i];

                $addition = ($currentOccupation->getDay() - $previousOccupation->getDay()) * 24;

                if ( $previousOccupation->getEndHour() - $addition != $currentOccupation->getStartHour() ) {
                    $gap = 1;
                    break;
                }
            }

            // we will decide what to do with a gap later

            if ( $startDay == $endDay) {
                $resultOccupations[] = (new DayOccupation())
                    ->setEmployee($oGroup[0]->getEmployee())
                    ->setDay($startDay)
                    // instead of throwing exception, we put few seconds for the start date, to show that the situation is not ok
                    ->setStartHour($startHour + $gap / 1000 )
                    ->setEndHour($endHour);
                continue;
            }
            // -- end of gap detecting block


            $resultOccupations[] = (new DayOccupation())
                ->setEmployee($oGroup[0]->getEmployee())
                ->setDay($startDay)
                ->setStartHour($startHour)
                ->setEndHour(24);

            // in between ( case when start and end date differs by more than 2 days
            for ( $day = $startDay+1; $day <= $endDay-1; $day++ ) {
                $resultOccupations[] = (new DayOccupation())
                    ->setEmployee($oGroup[0]->getEmployee())
                    ->setDay($day)
                    ->setStartHour(0)
                    ->setEndHour(24);
            }

            $resultOccupations[] = (new DayOccupation())
                ->setEmployee($oGroup[0]->getEmployee())
                ->setDay($endDay)
                ->setStartHour(0)
                ->setEndHour($endHour);
        }

        return $resultOccupations;
    }
}