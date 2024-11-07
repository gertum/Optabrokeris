<?php

namespace App\Domain\Roster;


use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\Attributes\MapTo;
use Spatie\DataTransferObject\DataTransferObject;

class SubjectData extends DataTransferObject
{
    public ?string $name;

    #[MapFrom('position_amount')]
    #[MapTo('position_amount')]
    public ?float $positionAmount;

    #[MapFrom('hours_in_month')]
    #[MapTo('hours_in_month')]
    public ?float $hoursInMonth;

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setPositionAmount(?float $positionAmount): self
    {
        $this->positionAmount = $positionAmount;

        return $this;
    }

    public function setHoursInMonth(?float $hoursInMonth): self
    {
        $this->hoursInMonth = $hoursInMonth;

        return $this;
    }
}