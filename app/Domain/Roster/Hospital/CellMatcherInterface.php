<?php

namespace App\Domain\Roster\Hospital;

interface CellMatcherInterface
{
    public function matchCell(Cell $cell, int $row, int $column): bool;
}