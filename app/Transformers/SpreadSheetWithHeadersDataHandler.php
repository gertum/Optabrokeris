<?php

namespace App\Transformers;

use App\Transformers\School\SchoolDataTransformer;

class SpreadSheetWithHeadersDataHandler implements SpreadSheetDataHandler
{
    public function spreadSheetToArray(string $excelFile): array
    {
        $sheetsRows = ExcelParser::getSheetsRows( $excelFile, 3 );
        $schoolDataTransformer = new SchoolDataTransformer();

        return $schoolDataTransformer->excelToJson($sheetsRows);
    }

    public function arrayToSpreadSheet(array $data, string $excelFile): void
    {
        // TODO: Implement arrayToSpreadSheet() method.
    }

}