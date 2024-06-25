<?php

namespace App\Domain\Roster;

use App\Util\BinarySearch;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;

class Schedule extends DataTransferObject
{
    /** @var Availability[] */
    #[CastWith(ArrayCaster::class, itemType: Availability::class)]
    public ?array $availabilityList = [];

    /**
     * @var Employee[]
     */
    #[CastWith(ArrayCaster::class, itemType: Employee::class)]
    public ?array $employeeList = [];

    /** @var Shift[] */
    #[CastWith(ArrayCaster::class, itemType: Shift::class)]
    public ?array $shiftList = [];


    public ?string $score = '-999999init/0hard/0soft';
    public ?ScheduleState $scheduleState;
    public ?string $solverState;

    public function setAvailabilityList(array $availabilityList): Schedule
    {
        $this->availabilityList = $availabilityList;
        return $this;
    }

    public function setEmployeeList($employeeList): Schedule
    {
        $this->employeeList = $employeeList;
        return $this;
    }

    public function setShiftList(array $shiftList): Schedule
    {
        $this->shiftList = $shiftList;
        return $this;
    }

    public function setScore(string $score): Schedule
    {
        $this->score = $score;
        return $this;
    }

    /**
     * @param mixed $scheduleState
     * @return Schedule
     */
    public function setScheduleState($scheduleState)
    {
        $this->scheduleState = $scheduleState;
        return $this;
    }

    /**
     * @param mixed $solverState
     * @return Schedule
     */
    public function setSolverState($solverState)
    {
        $this->solverState = $solverState;
        return $this;
    }

    /**
     * @param $start mixed currently it is string, later we will make DateTimeInterface or something like that.
     */
    public function findShiftByStartDate($start): ?Shift
    {
        BinarySearch::search($this->shiftList, $start, fn(Shift $shift, string $start) => $shift->start <=> $start);

        $filteredShifts = array_filter($this->shiftList, fn(Shift $shift) => $shift->start == $start);
        if (count($filteredShifts) == 0) {
            return null;
        }

        return reset($filteredShifts);
    }
}