<?php

namespace Tests\Unit\School;

use App\Transformers\School\SpreadSheetWithHeadersDataHandler;
use PHPUnit\Framework\TestCase;

class ExtractDataWithHeadersTest extends TestCase
{
    public function testExtract() {
        $h = new SpreadSheetWithHeadersDataHandler();
        $dataFromExcel = $h->spreadSheetToArray(__DIR__ . '/../data/SchoolDataWithHeaders.xlsx');

        $dataFromJson = json_decode(file_get_contents(__DIR__ . '/../data/data.json'), true);

        unset($dataFromJson['score']);
        unset($dataFromJson['solverStatus']);

        $this->assertEquals($dataFromJson, $dataFromExcel);
        // ignore score and solverStatus
    }

}