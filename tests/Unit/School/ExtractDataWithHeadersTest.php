<?php

namespace Tests\Unit\School;

use App\Exceptions\ValidateException;
use App\Transformers\School\SpreadSheetWithHeadersDataHandler;
use PHPUnit\Framework\TestCase;

class ExtractDataWithHeadersTest extends TestCase
{
    public function testExtract()
    {
        $h = new SpreadSheetWithHeadersDataHandler();
        $dataFromExcel = $h->spreadSheetToArray(__DIR__ . '/data/SchoolDataWithHeaders.xlsx');

        $dataFromJson = json_decode(file_get_contents(__DIR__ . '/data/data.json'), true);

        unset($dataFromJson['score']);
        unset($dataFromJson['solverStatus']);

        $this->assertEquals($dataFromJson, $dataFromExcel);
        // ignore score and solverStatus
    }

    public function testExtractLT()
    {
        $h = new SpreadSheetWithHeadersDataHandler();
        $dataFromExcel = $h->spreadSheetToArray(__DIR__ . '/data/SchoolDataWithHeadersLT.xlsx');

        $dataFromJson = json_decode(file_get_contents(__DIR__ . '/data/dataLT.json'), true);

        unset($dataFromJson['score']);
        unset($dataFromJson['solverStatus']);

        $this->assertEquals($dataFromJson, $dataFromExcel);
        // ignore score and solverStatus
    }

    public function testExtractLTinvalid()
    {
        $this->expectException(ValidateException::class);
        $this->expectExceptionMessageMatches('/missing column \[end time\]/i');
        $h = new SpreadSheetWithHeadersDataHandler();
        $h->spreadSheetToArray(__DIR__ . '/data/SchoolDataWithHeadersLTinvalid.xlsx');
    }
}