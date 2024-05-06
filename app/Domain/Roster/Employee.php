<?php

namespace App\Domain\Roster;

use Spatie\DataTransferObject\DataTransferObject;

class Employee extends DataTransferObject
{
    public ?string $name;
    public ?array $skillSet;

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

}