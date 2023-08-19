<?php

namespace Tests\Unit;

use App\Transformers\ExcelDataHandler;
use PHPUnit\Framework\TestCase;

class ExcelExporterTest extends TestCase
{

    public function testHandle() {
        $h = new ExcelDataHandler();
        $dataFromJson = json_decode(file_get_contents(__DIR__.'/data/data.json' ), true);


        $h->exportJsonToExcel($dataFromJson, __DIR__.'/data/TestExportSchoolData.xlsx');

        $transformedExcelData = $h->loadSchoolDataFromExcel( __DIR__.'/data/SchoolData.xlsx');
        $transformedTestExcelData = $h->loadSchoolDataFromExcel( __DIR__.'/data/TestExportSchoolData.xlsx');

        $this->assertEquals($transformedExcelData, $transformedTestExcelData);

    }
}