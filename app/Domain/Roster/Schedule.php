<?php

namespace App\Domain\Roster;

use Illuminate\Support\Collection;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;

class Schedule extends DataTransferObject
{
    public $availabilityList=[];

    /**
     * @var Employee[]
     */
    #[CastWith(ArrayCaster::class, itemType: Employee::class)]
    public ?array $employeeList;
    public $shiftList=[];
    public $score='';
    public $scheduleState;
    public $solverState;

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
}