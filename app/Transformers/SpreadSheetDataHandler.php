<?php

namespace App\Transformers;

interface SpreadSheetDataHandler
{
    // may be these two methods should be separated in to two classes

    public function spreadSheetToArray(string $excelFile): array;

    // TODO refactor last parameter as Job
    public function arrayToSpreadSheet(array $data, string $excelFile, string $originalFileContent=''): void;

    public function validateDataArray(array $data): void;
}
