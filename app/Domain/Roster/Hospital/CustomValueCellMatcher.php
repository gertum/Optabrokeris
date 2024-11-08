<?php

namespace App\Domain\Roster\Hospital;

class CustomValueCellMatcher implements CellMatcherInterface
{
    private string $pregToSearch;

    private int $row = -1;
    private int $column = -1;

    private bool $once;

    /**
     * @param string $pregToSearch
     */
    public function __construct(string $pregToSearch, bool $once=true)
    {
        $this->pregToSearch = $pregToSearch;
        $this->once = $once;
    }

    public function matchCell(Cell $cell, int $row, int $column): bool
    {
        if ( $this->once && $this->column >=0 && $this->row >= 0 ) {
            // we already found it
            return false;
        }
        if (preg_match($this->pregToSearch, $cell->value)) {
            $this->row = $row;
            $this->column = $column;
            return true;
        }

        return false;
    }

    public function getColumn(): int
    {
        return $this->column;
    }

    public function getRow(): int
    {
        return $this->row;
    }
}