<?php

namespace App\Transformers;

use App\Transformers\School\SchoolDataTransformer;

class SpreadSheetWithHeadersDataHandler implements SpreadSheetDataHandler
{
    public function spreadSheetToArray(string $excelFile): array
    {
        $sheetsRows = ExcelParser::getSheetsRows($excelFile, 3);

        // TODO validate sheetsRows

        $schoolDataTransformer = new SchoolDataTransformer();

        return $schoolDataTransformer->excelToJson($sheetsRows);
    }

    public function arrayToSpreadSheet(array $data, string $excelFile): void
    {
        $schoolDataTransformer = new SchoolDataTransformer();
        $excelData = $schoolDataTransformer->jsonToExcel($data);
        ExcelWriter::writeSheetsRows($excelFile, $excelData);
    }
}