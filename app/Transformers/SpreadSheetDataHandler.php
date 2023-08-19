<?php

namespace App\Transformers;

interface SpreadSheetDataHandler
{
    public function spreadSheetToArray(string $excelFile): array;

    public function arrayToSpreadSheet(array $data, string $excelFile): void;
}
