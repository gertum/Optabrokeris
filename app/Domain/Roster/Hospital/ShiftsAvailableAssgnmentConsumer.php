<?php

namespace App\Domain\Roster\Hospital;
use App\Domain\Roster\Employee;
use App\Domain\Roster\Shift;

class ShiftsAvailableAssgnmentConsumer implements AvailableAssignmentConsumer
{
    /** @var Shift[]  */
    private array $shifts;

    /**
     * @param Shift[] $shifts assume, that shifts are sorted by the increasing order.
     */
    public function __construct(array $shifts)
    {
        $this->shifts = $shifts;
    }


    public function setAssignment(string $from, string $till, Employee $employee): void
    {
        // TODO: Implement setAssignment() method.
    }


}