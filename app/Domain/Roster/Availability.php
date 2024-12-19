<?php

namespace App\Domain\Roster;

use Carbon\Carbon;
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
        $r->availabilityType = $this->availabilityType ?? '';
        $r->dateTill = $this->dateTill;
        $r->employeeName = $this->employee->name ?? '';

        return $r;
    }

    /**
     * memory cache
     */
    private ?Carbon $_carbonDate = null;

    public function getCarbonDate(): ?string
    {
        if ($this->_carbonDate == null) {
            if (is_string($this->date)) {
                $this->_carbonDate = Carbon::parse($this->date);
            }

            if ($this->date instanceof Carbon) {
                $this->_carbonDate = $this->date;
            }
        }

        return $this->_carbonDate;
    }


    /**
     * @param Carbon|string $date
     */
    public function compareToDate($date): int
    {
        if (is_string($this->date) && is_string($date)) {
            return $this->date <=> $date;
        }

        if ($date instanceof Carbon) {
            return $this->getCarbonDate() <=> $date;
        }

        if ( is_string($date)) {
            $carbonDate = Carbon::parse($date);
            return $this->getCarbonDate() <=> $carbonDate;
        }

        return 0;
    }

    public function compareTo(Availability $availability): int
    {
        $compareEmployee = ($this->employee->getSequenceNumber() <=> $availability->employee->getSequenceNumber());
        $compareDate = $this->compareToDate($availability->getCarbonDate());

        return $compareEmployee * 2 + $compareDate;
    }
}