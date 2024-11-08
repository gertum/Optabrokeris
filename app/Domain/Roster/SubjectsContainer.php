<?php

namespace App\Domain\Roster;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;

class SubjectsContainer extends DataTransferObject
{
    #[CastWith(ArrayCaster::class, itemType: SubjectData::class)]
    public ?array $subjects = [];

    public function setSubjects(?array $subjects): SubjectsContainer
    {
        $this->subjects = $subjects;
        return $this;
    }

    public function recalculateMonthHours(float $workDaysInMonth, bool $onlyZeros = false)
    {
        array_walk($this->subjects, fn(SubjectData $subject) => $subject->recalculateHoursInMonth($workDaysInMonth, $onlyZeros));
    }
}