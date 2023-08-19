<?php

namespace App\Transformers;

interface SpreadSheetDataHandler
{
    // may be these two methods should be separated in to two classes

    public function spreadSheetToArray(string $excelFile): array;

    public function arrayToSpreadSheet(array $data, string $excelFile): void;
}
