<?php

namespace App\Transformers\School;

use App\Transformers\ExcelParser;
use App\Transformers\ExcelWriter;
use App\Transformers\SpreadSheetDataHandler;

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