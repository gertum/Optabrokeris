<?php

namespace App\Domain\Roster;

use Spatie\DataTransferObject\DataTransferObject;

class Availability extends DataTransferObject
{
    const
        UNAVAILABLE = 'UNAVAILABLE',
        DESIRED = 'DESIRED',
        UNDESIRED = 'UNDESIRED',
        AVAILABLE = 'AVAILABLE';

    const AVAILABILITY_PRIORITIES = [
        self::UNAVAILABLE => 4,
        self::DESIRED => 3,
        self::UNDESIRED => 2,
        self::AVAILABLE => 1,
    ];

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

    public static function getAvailabilityTypePriority(string $availabilityTYpe): int
    {
        return self::AVAILABILITY_PRIORITIES[$availabilityTYpe];
    }

    public function getRepresentation(): AvailabilityRepresentation
    {
        $r = new AvailabilityRepresentation();
        $r->date = $this->date;
        $r->availabilityType = $this->availabilityType;
        $r->dateTill = $this->dateTill;
        $r->employeeName = $this->employee->name;

        return $r;
    }
}