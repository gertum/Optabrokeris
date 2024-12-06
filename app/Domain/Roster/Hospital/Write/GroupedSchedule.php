<?php

namespace App\Domain\Roster\Hospital\Write;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Schedule;
use App\Util\BinarySearch;
use App\Util\Grouper;
use Carbon\Carbon;

/**
 * @Deprecated: regular Schedule class has all required methods.
 */
class GroupedSchedule
{
    // may be should use occupations list here

    /** @var Availability[][] */
    private array $availabilitiesByEmployees = [];

    public function importSchedule(Schedule $schedule)
    {
        $this->availabilitiesByEmployees = Grouper::group(
            $schedule->availabilityList,
            fn(Availability $a) => $a->employee->getKey()
        );
        // must sort each group by the date
        foreach ($this->availabilitiesByEmployees as &$availabilities) {
            usort($availabilities, fn(Availability $a, Availability $b) => $a->date <=> $b->date);
        }
    }

    /**
     * @param string $employeeName
     * @return Availability[]
     */
    public function getAvailabilitiesByEmployee(string $employeeName): array
    {
        return $this->availabilitiesByEmployees[$employeeName];
    }

    public function findAvailability(string $employeeName, string $dateFormatted): ?Availability
    {
        $availabilities = &$this->availabilitiesByEmployees[$employeeName];

        if ($availabilities == null) {
            return null;
        }

        $foundIndex = BinarySearch::search(
            $availabilities,
            $dateFormatted,
            fn(Availability $a, string $date) => $a->date <=> $date,
            nearestDown: true,
            nearestUp: false
        );

        // special case to check bottom bound
        if ($foundIndex == 0) {
            $availability = $availabilities[$foundIndex];

            $searchDate = Carbon::parse($dateFormatted);
            $availabilityDate = Carbon::parse($availability->date);

            $difference = $searchDate->diff($availabilityDate);
            if ($difference->days > 0 || $difference->m > 0 || $difference->y > 0) {
                return null;
            }

            return $availability;
        }

        if ($foundIndex > 0) {
            return $availabilities[$foundIndex];
        }

        return null;
    }


}