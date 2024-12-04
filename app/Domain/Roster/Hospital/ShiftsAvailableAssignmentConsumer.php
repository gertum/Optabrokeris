<?php

namespace App\Domain\Roster\Hospital;

use App\Domain\Roster\Employee;
use App\Domain\Roster\Shift;
use App\Util\BinarySearch;
use Carbon\Carbon;

class ShiftsAvailableAssignmentConsumer implements AvailableAssignmentConsumer
{
    const DATE_FORMAT = 'Y-m-d\\TH:i:s';

    /** @var Shift[] */
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
        if ($till < $from) {
            $till =
                Carbon::createFromFormat(self::DATE_FORMAT, $till)
                ->addDay()
                ->format(self::DATE_FORMAT);
        }

        $lesserTill = Carbon::createFromFormat(self::DATE_FORMAT, $till)
            ->addMinutes(-1)
            ->format(self::DATE_FORMAT);

        $foundIndex = BinarySearch::search(
            $this->shifts,
            $from,
            fn(Shift $shift, string $from) => $shift->start <=> $from,
            true
        );

        if ($foundIndex == -1) {
            return;
        }

        while ($foundIndex < count($this->shifts) && $this->shifts[$foundIndex]->start <= $lesserTill) {
            if ($this->shifts[$foundIndex]->end >= $from) {
                $this->shifts[$foundIndex]->setEmployee($employee);
            }
            $foundIndex++;
        }
    }
}