<?php

namespace App\Domain\Roster\Report;

class ScheduleReport
{
// TODO
    private $id;
    private string $name;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return ScheduleReport
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): ScheduleReport
    {
        $this->name = $name;
        return $this;
    }
}