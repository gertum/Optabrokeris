<?php

namespace App\Domain\Roster;

use App\Util\BinarySearch;
use App\Util\DateRecognizer;
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
        $index = BinarySearch::search($this->shiftList, $start, fn(Shift $shift, string $start) =>
            $shift->start <=> $start,
            true);

        if ( $index < 0 ) {
            return null;
        }

        // go up till $start is between begin and end
        while ( $this->shiftList[$index]->start < $start ) {
            $index++;
        }

        return $this->shiftList[$index];
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
            $employee->setPositionAmount($subject->getPositionAmount() ?? 0);
            $employee->setWorkingHoursPerDay($subject->getHoursInDay() ?? 0);
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
        usort($this->availabilityList,fn(Availability $a, Availability $b) => $a->compareTo($b));
    }

    /**
     * Works only when all employees have assigned sequence number and availabilities array is sorted.
     * For example after assignEmployeesSequenceNumbers and sortAvailabilities are called.
     */
    public function findAvailability(
        string $employeeName,
        string $startDate,
        bool $nearestDown = false,
        bool $nearestUp = false
    ): ?Availability {
        $employee = $this->findEmployee($employeeName);
        if ($employee == null) {
            return null;
        }

        $availabilityIndex = BinarySearch::search(
            $this->availabilityList,
            (new Availability())->setEmployee($employee)->setDate($startDate),
            fn(Availability $availability, $searchAvailability) => $availability->compareTo($searchAvailability),
            $nearestDown,
            $nearestUp
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


    /**
     * @param float[] $shiftsBounds
     * @return Shift[]
     * @deprecated incorrect and unusable
     */
    public function recalculateShiftsByBounds(array $shiftsBounds ) : array {
        // We assume that the following is valid for the TWO CONSECUTIVE shifts:
        // previous end time is the same as the next start time.

        // The only reason we use previous shifts list is that the employee might be assigned already.
        // better way is to build completely new shifts lists and somehow assign previous employees to the new list.

        if ( count($this->shiftList) == 0) {
            return [];
        }

        $newShiftsList = [];
        $previousShift = clone $this->shiftList[0];
        for ($i = 1; $i < count($this->shiftList); $i++) {
            $currentShift = $this->shiftList[$i];

            // decide if we need to join shifts or not to join
            $previousEndDate = Carbon::parse($previousShift->end);
            $previousEndBound = DateRecognizer::calculateFloatingHourOfDate($previousEndDate);
            if ( !in_array($previousEndBound, $shiftsBounds) ) {
                $previousShift->setEnd($currentShift->end);
            }
            else {
                $newShiftsList[] = $previousShift;
                $previousShift = clone $currentShift;
            }
        }
        $newShiftsList[] = $previousShift;

        return $newShiftsList;
    }

    /**
     * @return Availability[]
     */
    public function recalculateAvailabilitiesByShifts(): array {
        $this->referenceEmployeesToAvailabilities();
        $this->assignEmployeesSequenceNumbers();
        $this->sortAvailabilities();
        $availabilities = [];
        $id = 1;
        foreach ($this->employeeList as $employee) {
            foreach ($this->shiftList as $shift) {
                $foundAvailability = $this->findAvailability($employee->name, $shift->start, true);

                $availabilities[] = (new Availability())
                    ->setEmployee($employee)
                    ->setDate($shift->start)
                    ->setDateTill($shift->end)
                    ->setAvailabilityType($foundAvailability->availabilityType)
                    ->setId($id++)
                ;

            }
        }
        return $availabilities;
    }

    public function referenceEmployeesToAvailabilities() : void {
        $employeesByNames = MapBuilder::buildMap($this->employeeList, fn(Employee $e)=> $e->name );
        foreach ($this->availabilityList as $availability) {
            if ( !array_key_exists($availability->employee->name, $employeesByNames)) {
                continue;
            }
            $availability->setEmployee( $employeesByNames[$availability->employee->name]);
        }
    }
}