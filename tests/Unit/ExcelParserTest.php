<?php

namespace Tests\Unit;

use App\Transformers\ExcelSchoolDataHandler;
use Tests\TestCase;

class ExcelParserTest extends TestCase
{

    public function testParse()
    {
        $h = new ExcelSchoolDataHandler();
        $transformedExcelData = $h->spreadSheetToArray(__DIR__ . '/data/SchoolData.xlsx');
        $dataFromJson = json_decode(file_get_contents(__DIR__ . '/data/data.json'), true);
        // ignore score and solverStatus
        unset($dataFromJson["score"], $dataFromJson["solverStatus"]);

        $this->assertEquals($dataFromJson, $transformedExcelData);
    }
}
