<?php

namespace App\Domain\Roster\Hospital;

/**
 * Parse starting marker.
 */
class EilNrTitle
{
    public const EIL_NR_MARKER = '/eil.\s*nr/';

//    private ?Cell $foundCell=null;

    private int $column;
    private int $row;

    // will calculate somehow automatically later
    private int $rowSpan= 2;

    public function getColumn(): int
    {
        return $this->column;
    }

    public function setColumn(int $column): EilNrTitle
    {
        $this->column = $column;
        return $this;
    }

    public function getRow(): int
    {
        return $this->row;
    }

    public function setRow(int $row): EilNrTitle
    {
        $this->row = $row;
        return $this;
    }

    public function getRowSpan(): int
    {
        return $this->rowSpan;
    }

    public function setRowSpan(int $rowSpan): EilNrTitle
    {
        $this->rowSpan = $rowSpan;
        return $this;
    }
}