<?php

namespace App\Domain\Roster\Hospital;

use App\Domain\Roster\Employee;

interface AvailableAssignmentConsumer
{
    /**
     * We use string type for dates, because this interface is intended to be called from parser.
     */
    public function setAssignment(string $from, string $till, Employee $employee): void;

}