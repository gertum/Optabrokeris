<?php

namespace App\Domain\Roster;

use Spatie\DataTransferObject\DataTransferObject;

/**
 * TODO move to a more common namespace ( not only Roster )
 * Used to parse json data, stored in Job and in User
 */
class Profile extends DataTransferObject
{
    const WRITE_TYPE_ORIGINAL_FILE = 'original_file';
    const WRITE_TYPE_TEMPLATE_FILE = 'template_file';


    /**
     * @var float[] Hour value.
     */
    public array $shiftBounds = [];

    public string $writeType = self::WRITE_TYPE_ORIGINAL_FILE;

    public function getShiftBounds(): array
    {
        return $this->shiftBounds;
    }

    public function setShiftBounds(array $shiftBounds): self
    {
        $this->shiftBounds = $shiftBounds;

        return $this;
    }

    public function setWriteType(string $writeType): Profile
    {
        $this->writeType = $writeType;
        return $this;
    }
}