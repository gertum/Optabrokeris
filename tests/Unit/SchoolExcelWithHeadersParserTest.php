<?php

namespace Tests\Unit;

use App\Transformers\ExcelSchoolDataHandler;
use App\Transformers\ExcelSchoolDataWithHeadersHandler;
use PHPUnit\Framework\TestCase;

class SchoolExcelWithHeadersParserTest extends TestCase
{
    public function testParseExcel() {
        $h = new ExcelSchoolDataWithHeadersHandler();
        $transformedExcelData = $h->spreadSheetToArray(__DIR__ . '/data/Exc');
        $dataFromJson = json_decode(file_get_contents(__DIR__ . '/data/data.json'), true);

        $this->assertEquals($dataFromJson, $transformedExcelData);



    }
}