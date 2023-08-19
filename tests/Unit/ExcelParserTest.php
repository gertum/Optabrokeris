<?php

namespace Tests\Unit;

use App\Transformers\ExcelDataHandler;
use Shuchkin\SimpleXLSX;
use Tests\TestCase;

class ExcelParserTest extends TestCase
{
    public function _testParse(){


        if ( $xlsx = SimpleXLSX::parse(__DIR__.'/data/SchoolData.xlsx') ) {
            print_r( $xlsx->rows() );
        } else {
            echo SimpleXLSX::parseError();
        }
        $this->assertTrue(true);
    }

    public function testHandle() {
        $h = new ExcelDataHandler();

        $transformedExcelData = $h->loadSchoolDataFromExcel( __DIR__.'/data/SchoolData.xlsx');
        $dataFromJson = json_decode(file_get_contents(__DIR__.'/data/data.json' ), true);
        unset($dataFromJson["score"], $dataFromJson["solverStatus"]);
        $this->assertEquals($dataFromJson, $transformedExcelData);
    }
}