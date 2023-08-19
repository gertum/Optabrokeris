<?php

namespace Tests\Unit;

use App\Transformers\ExcelSchoolDataHandler;
use PHPUnit\Framework\TestCase;

class ExcelExporterTest extends TestCase
{

    public function testHandle() {
        $h = new ExcelSchoolDataHandler();
        $dataFromJson = json_decode(file_get_contents(__DIR__.'/data/data.json' ), true);

        $exportedFileName = sprintf( 'storage/app/TestExportSchoolData_%s.xlsx', time() );
        $h->arrayToSpreadSheet($dataFromJson, $exportedFileName);
        $transformedTestExcelData = $h->spreadSheetToArray( $exportedFileName);

        unset($dataFromJson["score"], $dataFromJson["solverStatus"]);

        $this->assertEquals($dataFromJson, $transformedTestExcelData);
    }
}
