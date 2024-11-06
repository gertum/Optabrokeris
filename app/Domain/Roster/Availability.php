<?php

namespace App\Domain\Roster;

use Spatie\DataTransferObject\DataTransferObject;

class Availability extends DataTransferObject
{
    const     DESIRED = 'DESIRED',
        AVAILABLE = 'AVAILABLE',
        UNDESIRED = 'UNDESIRED',
        UNAVAILABLE = 'UNAVAILABLE';

    public ?int $id = 0;
    public ?Employee $employee = null;
    public $date;
    public ?string $availabilityType;

    public $dateTill;

    public function setId(int $id): Availability
    {
        $this->id = $id;
        return $this;
    }

    public function setEmployee(Employee $employee): Availability
    {
        $this->employee = $employee;
        return $this;
    }


    /**
     * @param mixed $date
     * @return Availability
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    public function setAvailabilityType(string $availabilityType): Availability
    {
        $this->availabilityType = $availabilityType;
        return $this;
    }

    public function setDateTill($dateTill): self
    {
        $this->dateTill = $dateTill;

        return $this;
    }


}