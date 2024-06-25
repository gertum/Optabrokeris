<?php

namespace App\Domain\Roster\Hospital;

/**
 * This interface is used to generalize classes which searches cell by various marks.
 */
interface CellMatcherInterface
{
    public function matchCell(Cell $cell, int $row, int $column): bool;
}