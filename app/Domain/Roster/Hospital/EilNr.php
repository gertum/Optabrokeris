<?php

namespace App\Domain\Roster\Hospital;

class EilNr
{
    private int $value;
    private int $row;
    private int $rowSpan=2;
    private int $column;

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): EilNr
    {
        $this->value = $value;
        return $this;
    }

    public function getRow(): int
    {
        return $this->row;
    }

    public function setRow(int $row): EilNr
    {
        $this->row = $row;
        return $this;
    }

    public function getRowSpan(): int
    {
        return $this->rowSpan;
    }

    public function setRowSpan(int $rowSpan): EilNr
    {
        $this->rowSpan = $rowSpan;
        return $this;
    }

    public function getColumn(): int
    {
        return $this->column;
    }

    public function setColumn(int $column): EilNr
    {
        $this->column = $column;
        return $this;
    }
}