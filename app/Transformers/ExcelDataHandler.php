<?php

namespace App\Transformers;

use PhpParser\Node\Expr\Array_;
use Shuchkin\SimpleXLSX;
use Exception;

class ExcelDataHandler
{
    public function loadSchoolDataFromExcel(string $excelFile): array
    {

        if ($xlsx = SimpleXLSX::parse($excelFile)) {
            print_r($xlsx->rows());
//            $sheetCount = $xlsx->sheetsCount();
//            for ($i = 0; $i< $sheetCount; $i++)
//            {
//                $sheetName = $xlsx->sheetName($i);
//            }
            $transformedExcelSheet = $xlsx->rows();
            $transformedExcelSheet = array_map(function ($transformedExcelColumn)
            {
                return ['id' => $transformedExcelColumn[0],
                    'dayOfWeek' => $transformedExcelColumn[1],
                    'startTime' => $transformedExcelColumn[2],
                    'endTime' => $transformedExcelColumn[3]];
            }, $transformedExcelSheet

            );


            $transformedExcelSheet1 = $xlsx->rows(1);
            $transformedExcelSheet2 = $xlsx->rows(2);
            $transformedExcelData= array($transformedExcelSheet,$transformedExcelSheet1,$transformedExcelSheet2);
            // TODO multiple requests from xls for each sheet
            return $transformedExcelData;
        }
        throw new Exception(SimpleXLSX::parseError());

    }
}