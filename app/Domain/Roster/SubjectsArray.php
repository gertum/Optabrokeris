<?php

namespace App\Domain\Roster;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;

class SubjectsArray extends DataTransferObject
{
    #[CastWith(ArrayCaster::class, itemType: SubjectData::class)]
    public ?array $subjects=[];

    public function setSubjects(?array $subjects): SubjectsArray
    {
        $this->subjects = $subjects;
        return $this;
    }
}