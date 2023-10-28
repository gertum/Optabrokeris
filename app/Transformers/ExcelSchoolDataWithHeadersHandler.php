<?php

namespace App\Transformers;

use Shuchkin\SimpleXLSX;

class ExcelSchoolDataWithHeadersHandler implements SpreadSheetDataHandler
{
    public function spreadSheetToArray(string $excelFile): array
    {
//        // TODO: Implement spreadSheetToArray() method.
//        $xlsx = SimpleXLSX::parse($excelFile);
//        if (!$xlsx) {
//            return  [];
//        }
//
//        $timeslots = $xlsx->rows(0);
//        $roomList = $xlsx->rows(1);
//        $lessonList = $xlsx->rows(2);

        $excelParser = new ExcelParser();
        $sheets = $excelParser->getSheetsRows($excelFile, 3);

        return [];
    }

    public function arrayToSpreadSheet(array $data, string $excelFile): void
    {
        // TODO: Implement arrayToSpreadSheet() method.
    }


}