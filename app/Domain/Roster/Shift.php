<?php

namespace App\Domain\Roster;

use Spatie\DataTransferObject\DataTransferObject;

class Shift  extends DataTransferObject
{
    public int $id = 0;
    // TODO date ?
    public $start = null;
    public $end = null;
    public ?string $location;
    public ?string $requiredSkill = null;
    public ?Employee $employee = null;

    public function setId(int $id): Shift
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param null $start
     * @return Shift
     */
    public function setStart($start)
    {
        $this->start = $start;
        return $this;
    }

    /**
     * @param null $end
     * @return Shift
     */
    public function setEnd($end)
    {
        $this->end = $end;
        return $this;
    }

    public function setLocation(string $location): Shift
    {
        $this->location = $location;
        return $this;
    }

    public function setRequiredSkill(?string $requiredSkill): Shift
    {
        $this->requiredSkill = $requiredSkill;
        return $this;
    }

    public function setEmployee(?Employee $employee): Shift
    {
        $this->employee = $employee;
        return $this;
    }
}