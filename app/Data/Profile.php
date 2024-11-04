<?php

namespace App\Data;

use Spatie\DataTransferObject\DataTransferObject;

/**
 * Used to parse json data, stored in Job and in User
 */
class Profile extends DataTransferObject
{
    /**
     * @var float[] Hour value.
     */
    public array $shiftBounds=[];

    public function getShiftBounds(): array
    {
        return $this->shiftBounds;
    }

    public function setShiftBounds(array $shiftBounds): self
    {
        $this->shiftBounds = $shiftBounds;

        return $this;
    }
}