<?php

namespace App\Domain\Roster\Hospital\Write;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Schedule;
use App\Util\BinarySearch;
use App\Util\Grouper;

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
        $availabilities = & $this->availabilitiesByEmployees[$employeeName];
        $foundIndex = BinarySearch::search(
            $availabilities,
            $dateFormatted,
            fn(Availability $a, string $date) => $a->date <=> $date
        );

        if ( $foundIndex >= 0 ) {
            return $availabilities[$foundIndex];
        }

        return null;
    }
}