<?php

namespace App\Domain\Roster\Hospital;

use Shuchkin\SimpleXLSX;

class ExcelWrapper
{
    private array $rowsEx = [];
    private ?SimpleXLSX $xlsx = null;

    public static function parse(string $file): self
    {
        $wrapper = new ExcelWrapper();

        $wrapper->xlsx = SimpleXLSX::parse($file);
        $wrapper->rowsEx = $wrapper->xlsx->rowsEx();

        return $wrapper;
    }

    public function getRowsEx(): array
    {
        return $this->rowsEx;
    }

    public function getXlsx(): ?SimpleXLSX
    {
        return $this->xlsx;
    }

    public function getCell($row, $column): Cell
    {
        return new Cell($this->rowsEx[$row][$column]);
    }
}