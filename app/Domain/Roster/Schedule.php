<?php

namespace App\Domain\Roster;

use App\Util\BinarySearch;
use App\Util\MapBuilder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;

class Schedule extends DataTransferObject
{
    const TARGET_DATE_FORMAT = 'Y-m-d\\TH:i:s';

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

    public function fillSkills(string $skill): self
    {
        foreach ($this->shiftList as $shift) {
            $shift->setRequiredSkill($skill);
        }

        foreach ($this->employeeList as $employee) {
            $employee->skillSet = [$skill];
        }

        return $this;
    }

    public function fillLocation(string $location): self
    {
        foreach ($this->shiftList as $shift) {
            $shift->setLocation($location);
        }
        return $this;
    }

    public function getEmployeesNames(): array
    {
        return array_map(fn(Employee $e) => $e->name, $this->employeeList);
    }

    /**
     * @param SubjectDataInterface[] $subjects
     * @return self
     */
    public function fillEmployeesWithSubjectsData(array $subjects): self
    {
        /** @var SubjectDataInterface[] $subjectByName */
        $subjectByName = MapBuilder::buildMap($subjects, fn(SubjectDataInterface $subject) => $subject->getName());

        foreach ($this->employeeList as $employee) {
            if (!array_key_exists($employee->name, $subjectByName)) {
                // error message , or exception
//                throw new SolverDataException(
//                    sprintf('could not find matching subject for %s employee', $employee->name)
//                );
                Log::warning(sprintf('could not find matching subject for %s employee', $employee->name));
                continue;
            }
            $subject = $subjectByName[$employee->name];

            $employee->setMaxWorkingHours($subject->getHoursInMonth());
            $employee->setPositionAmount($subject->getPositionAmount());
            $employee->setWorkingHoursPerDay($subject->getHoursInDay());
        }

        return $this;
    }

    public function detectMonthDate(): ?Carbon
    {
        // go through shifts list if we find 2 shifts in the same month, then the month date is found
        $localFormat = 'Y-m';
        $yearMonthSet = [];
        foreach ($this->shiftList as $shift) {
            $date = Carbon::createFromFormat(self::TARGET_DATE_FORMAT, $shift->start);
            $yearMonth = $date->format($localFormat);

            if (!array_key_exists($yearMonth, $yearMonthSet)) {
                $yearMonthSet[$yearMonth] = 0;
            }

            $yearMonthSet[$yearMonth] = $yearMonthSet[$yearMonth] + 1;

            if ($yearMonthSet[$yearMonth] >= 2) {
                break;
            }
        }

        $dateSelected = null;
        foreach ($yearMonthSet as $formattedDate => $count) {
            $dateSelected = Carbon::createFromFormat($localFormat, $formattedDate);
            if ($count >= 2) {
                break;
            }
        }

        if ($dateSelected == null) {
            return $dateSelected;
        }

        // leave only year and month
        return Carbon::create($dateSelected->year, $dateSelected->month);
    }

    /**
     * @return AvailabilityRepresentation[]
     */
    public function getAvailabilitiesRepresentations(): array
    {
        return array_map(fn(Availability $a) => $a->getRepresentation(), $this->availabilityList);
    }

    public function assignEmployeesSequenceNumbers()
    {
        for ($i = 0; $i < count($this->employeeList); $i++) {
            $this->employeeList[$i]->setSequenceNumber($i + 1);
        }
    }

    public function sortAvailabilities()
    {
        usort(
            $this->availabilityList,
            fn(Availability $a, Availability $b) => (
                    $a->employee->getSequenceNumber() <=> $b->employee->getSequenceNumber()
                ) * 2 + ($a->date <=> $b->date)
        );
    }

    /**
     * Works only when all employees have assigned sequence number and availabilities array is sorted.
     * For example after assignEmployeesSequenceNumbers and sortAvailabilities are called.
     */
    public function findAvailability(string $employeeName, string $startDate): ?Availability
    {
        $employee = $this->findEmployee($employeeName);
        if ($employee == null) {
            return null;
        }

        $availabilityIndex = BinarySearch::search(
            $this->availabilityList,
            ['seq' => $employee->getSequenceNumber(), 'date' => $startDate],
            fn(Availability $availability, $searchParams) => (
                    $availability->employee->getSequenceNumber() <=> $searchParams['seq']
                ) * 2 + ($availability->date <=> $searchParams['date'])
        );

        if ($availabilityIndex < 0) {
            return null;
        }

        return $this->availabilityList[$availabilityIndex];
    }

    public function findEmployee(string $employeeName): ?Employee
    {
        foreach ($this->employeeList as $employee) {
            if ($employee->name == $employeeName) {
                return $employee;
            }
        }
        return null;
    }
}