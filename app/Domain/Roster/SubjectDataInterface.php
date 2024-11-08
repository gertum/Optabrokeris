<?php

namespace App\Domain\Roster;

interface SubjectDataInterface
{
    public function setName(?string $name): self;

    public function setPositionAmount(?float $positionAmount): self;

    public function setHoursInMonth(?float $hoursInMonth): self;

    public function setHoursInDay(?float $hoursInDay): self;

    public function getName(): ?string;

    public function getPositionAmount(): ?float;

    public function getHoursInMonth(): ?float;

    public function getHoursInDay(): ?float;
}