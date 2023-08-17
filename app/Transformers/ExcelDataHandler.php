<?php

namespace App\Transformers;

use Shuchkin\SimpleXLSX;

class ExcelDataHandler
{
    public function loadSchoolDataFromExcel(string $excelFile) : array {

        if ($xlsx = SimpleXLSX::parse($excelFile) ) {
            print_r($xlsx->rows());
            $transformedExcelData = $xlsx->rows();
            // TODO multiple requests from xls for each sehhet
            return $transformedExcelData;
        }
        return []; // TODO handle error
    }
}