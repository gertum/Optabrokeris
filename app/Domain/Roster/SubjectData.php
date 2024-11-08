<?php

namespace App\Domain\Roster;


use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\Attributes\MapTo;
use Spatie\DataTransferObject\DataTransferObject;

class SubjectData extends DataTransferObject implements SubjectDataInterface
{
    public ?string $name;

    #[MapFrom('position_amount')]
    #[MapTo('position_amount')]
    public ?float $positionAmount;

    #[MapFrom('hours_in_month')]
    #[MapTo('hours_in_month')]
    public ?float $hoursInMonth=0;

    #[MapFrom('hours_in_day')]
    #[MapTo('hours_in_day')]
    public ?float $hoursInDay;

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

    public function setHoursInDay(?float $hoursInDay): SubjectData
    {
        $this->hoursInDay = $hoursInDay;

        return $this;
    }

    public function recalculateHoursInMonth(float $totalDaysInMonth, bool $onlyZeros = false): void {
        if ( $onlyZeros && $this->hoursInMonth != 0  ) {
            return;
        }
        $this->hoursInMonth = $this->hoursInDay * $totalDaysInMonth;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPositionAmount(): ?float
    {
        return $this->positionAmount;
    }

    public function getHoursInMonth(): ?float
    {
        return $this->hoursInMonth;
    }

    public function getHoursInDay(): ?float
    {
        return $this->hoursInDay;
    }
}