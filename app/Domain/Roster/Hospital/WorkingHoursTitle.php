<?php

namespace App\Domain\Roster\Hospital;

class WorkingHoursTitle implements CellMatcherInterface
{
    /**
     * Will change to regexp if needed later.
     */
    public const COLUMN_TITLE = 'Darbo valandų per mėnesį';

    private int $row;
    private int $column;

    public function getRow(): int
    {
        return $this->row;
    }

    public function setRow(int $row): WorkingHoursTitle
    {
        $this->row = $row;
        return $this;
    }

    public function getColumn(): int
    {
        return $this->column;
    }

    public function setColumn(int $column): WorkingHoursTitle
    {
        $this->column = $column;
        return $this;
    }

    public function matchCell(Cell $cell, int $row, int $column): bool
    {
        if ($cell->value == self::COLUMN_TITLE) {
            $this->row = $row;
            $this->column = $column;
            return true;
        }

        return false;
    }
}