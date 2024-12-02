<?php

namespace App\Transformers\School;

use App\Domain\Roster\Profile;
use App\Models\Job;
use App\Transformers\ExcelParser;
use App\Transformers\ExcelWriter;
use App\Transformers\SpreadSheetDataHandler;

class SpreadSheetWithHeadersDataHandler implements SpreadSheetDataHandler
{
    public function spreadSheetToArray(string $excelFile, ?Profile $profileObj = null): array
    {
        $sheetsRows = ExcelParser::getSheetsRows($excelFile, 3);
        $schoolDataTransformer = new SchoolDataTransformer();

        return $schoolDataTransformer->excelToJson($sheetsRows);
    }

    public function arrayToSpreadSheet(array $data, string $excelFile, ?Job $job): void
    {
        $schoolDataTransformer = new SchoolDataTransformer();
        $excelData = $schoolDataTransformer->jsonToExcel($data);
        ExcelWriter::writeSheetsRows($excelFile, $excelData);
    }

    public function validateDataArray(array $data): void
    {
        CellValidator::validateCells($data);
    }
}