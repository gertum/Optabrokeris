<?php

namespace Tests\Unit\data;

use App\Transformers\SpreadSheetDataHandler;

class ExcelSchoolDataWithHeadersHandler implements SpreadSheetDataHandler
{
    public function spreadSheetToArray(string $excelFile): array
    {
        // TODO: Implement spreadSheetToArray() method.
        return [];
    }

    public function arrayToSpreadSheet(array $data, string $excelFile): void
    {
        // TODO: Implement arrayToSpreadSheet() method.
    }


}