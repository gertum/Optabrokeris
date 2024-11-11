<?php

namespace App\Domain\Roster\Hospital\Write;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Schedule;

class GroupedSchedule
{
    // may be should use occupations list here

    /** @var Availability[][] */
    private array $availabilitiesByEmployees = [];

    public function importSchedule(Schedule $schedule)
    {
        // TODO
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
        return null;
    }
}