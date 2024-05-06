<?php

namespace App\Domain\Roster;

use Spatie\DataTransferObject\DataTransferObject;

class Employee extends DataTransferObject
{
    public ?string $name;
    public ?array $skillSet;

    public $date;
    public $availabilityType;

    public function setName(string $name): Employee
    {
        $this->name = $name;
        return $this;
    }

    public function setSkillSet(array $skillSet): Employee
    {
        $this->skillSet = $skillSet;
        return $this;
    }

    /**
     * @param mixed $date
     * @return Employee
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @param mixed $availabilityType
     * @return Employee
     */
    public function setAvailabilityType($availabilityType)
    {
        $this->availabilityType = $availabilityType;
        return $this;
    }
}