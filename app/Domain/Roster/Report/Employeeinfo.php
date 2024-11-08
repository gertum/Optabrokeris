<?php

namespace App\Domain\Roster\Report;

use App\Domain\Roster\Employee;

/**
 * This class should be used for a backend GUI, which is not implemented yet
 */
class Employeeinfo
{
    private Employee $employee;

    private float $hoursTotal;

    public function __construct(Employee $employee, float $hoursTotal)
    {
        $this->employee = $employee;
        $this->hoursTotal = $hoursTotal;
    }


    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    public function setEmployee(Employee $employee): Employeeinfo
    {
        $this->employee = $employee;
        return $this;
    }

    public function getHoursTotal(): float
    {
        return $this->hoursTotal;
    }

    public function setHoursTotal(float $hoursTotal): Employeeinfo
    {
        $this->hoursTotal = $hoursTotal;
        return $this;
    }

    public function addHours($hours) {
        $this->hoursTotal += $hours;
    }
}