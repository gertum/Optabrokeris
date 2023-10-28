<?php

namespace App\Transformers;

use Shuchkin\SimpleXLSX;

class ExcelSchoolDataWithHeadersHandler implements SpreadSheetDataHandler
{
    public function spreadSheetToArray(string $excelFile): array
    {
        // TODO: Implement spreadSheetToArray() method.
        $xlsx = SimpleXLSX::parse($excelFile);
        if (!$xlsx) {
            return  [];
        }

        return [];
    }

    public function arrayToSpreadSheet(array $data, string $excelFile): void
    {
        // TODO: Implement arrayToSpreadSheet() method.
    }


}