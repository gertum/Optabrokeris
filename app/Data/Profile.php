<?php

namespace App\Data;

class Profile
{
    /**
     * @var float[] Hour value.
     */
    private array $shiftBounds=[];

    public function getShiftBounds(): array
    {
        return $this->shiftBounds;
    }

    public function setShiftBounds(array $shiftBounds): void
    {
        $this->shiftBounds = $shiftBounds;
    }
}