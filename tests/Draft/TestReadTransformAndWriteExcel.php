<?php

namespace Tests\Draft;

use App\Transformers\School\SpreadSheetWithHeadersDataHandler;
use Tests\TestCase;

class TestReadTransformAndWriteExcel extends TestCase
{
    public function testReadAndWrite()
    {
        $file = __DIR__ . '/SchoolDataWithHeaders.xlsx';
        $rezFile = __DIR__ . '/Rez2SchoolDataWithHeaders.xlsx';
        $h = new SpreadSheetWithHeadersDataHandler();
        $jsonArray = $h->spreadSheetToArray($file);
        $h->arrayToSpreadSheet($jsonArray, $rezFile);
        $this->assertTrue(true);
    }
}