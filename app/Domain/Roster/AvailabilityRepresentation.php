<?php

namespace App\Domain\Roster;

class AvailabilityRepresentation
{
    public string $employeeName = '';
    public string $date = '';
    public ?string $dateTill = '';
    public string $availabilityType = '';
}