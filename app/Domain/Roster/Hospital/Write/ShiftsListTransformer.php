<?php

namespace App\Domain\Roster\Hospital\Write;

use App\Domain\Roster\Shift;
use App\Exceptions\ExcelParseException;
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

            $startDate = Carbon::create($shift->start);
            $endDate = Carbon::create($shift->end);

            if ($startDate > $endDate) {
                // TODO use different exception
                throw new ExcelParseException(sprintf('When writing results shift %s start date %s is greater than end date %s', $shift->id, $startDate, $endDate));
            }

            if ($startDate->day == $endDate->day) {
                $occupations[] = (new DayOccupation())
                    ->setEmployee($shift->employee)
                    ->setDay($startDate->day)
                    ->setStartTime($startDate)
                    ->setEndTime($endDate)
                    ->setDateFormatted($startDate->format('Y-m-d'));
                continue;
            }

            // ----------------------------- split part ------------------------------------
            $occupations[] = (new DayOccupation())
                ->setEmployee($shift->employee)
                ->setDay($startDate->day)
                ->setStartTime(clone $startDate)
                ->setEndTime(Carbon::create($startDate->year, $startDate->month, $startDate->day, 24))
                ->setDateFormatted($startDate->format('Y-m-d'));

            $startDate->addDay();
//            if ($endDate->diff($startDate)->days > 2) {
//                throw new ExcelParseException(sprintf('Too big gap int the shift\'s %s start %s and end %s times',
//                    $shift->id, $startDate, $endDate));
//            }
            while ($startDate->day != $endDate->day) {
                $occupations[] = (new DayOccupation())
                    ->setEmployee($shift->employee)
                    ->setDay($startDate->day)
                    ->setStartTime(Carbon::create($startDate->year, $startDate->month, $startDate->day, 0))
                    ->setEndTime(Carbon::create($startDate->year, $startDate->month, $startDate->day, 24))
                    ->setDateFormatted($startDate->format('Y-m-d'));
                $startDate->addDay();
            }
            $occupations[] = (new DayOccupation())
                ->setEmployee($shift->employee)
                ->setDay($startDate->day)
                ->setStartTime(Carbon::create($startDate->year, $startDate->month, $startDate->day, 0))
                ->setEndTime($endDate)
                ->setDateFormatted($startDate->format('Y-m-d'));

            // ----------------------- end of split part ------------------------------------
        }

        // ------------------- join part ----------------------------

        usort($occupations, fn(DayOccupation $a, DayOccupation $b) => $a->getStartTime() <=> $b->getStartTime());

        /** @var DayOccupation[][] $groupedOccupations */
        $groupedOccupations = Grouper::group($occupations, fn(DayOccupation $o) => $o->createGroupKey());

        /** @var DayOccupation[] $resultOccupations */
        $resultOccupations = [];

        foreach ($groupedOccupations as $oGroup) {
            $startTime = $oGroup[0]->getStartTime();
            $endTime = last($oGroup)->getEndTime();

            if ($startTime == $endTime) {
                continue;
            }
            $resultOccupations[] = (new DayOccupation())
                ->setEmployee($oGroup[0]->getEmployee())
                ->setDay($startTime->day)
                ->setStartTime($startTime)
                ->setEndTime($endTime)
                ->setDateFormatted($oGroup[0]->getDateFormatted())
                ->calculateStartHour()
                ->calculateEndHour()
                ->fixEndHour();

        }

        // -------------------------- end of join part -----------------------------------

        return $resultOccupations;
    }

}