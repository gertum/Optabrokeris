<?php

namespace Tests\Unit\School;

use App\Transformers\ExcelParser;
use App\Transformers\School\SchoolDataTransformer;
use PHPUnit\Framework\TestCase;

class ExtractDataWithHeadersTest extends TestCase
{
    public function testExtract() {
        $sheetsRows = ExcelParser::getSheetsRows( __DIR__ . '/../data/SchoolDataWithHeaders.xlsx', 3 );
        $schoolDataTransformer = new SchoolDataTransformer();
        $dataFromExcel = $schoolDataTransformer->excelToJson($sheetsRows);
        $dataFromJson = json_decode(file_get_contents(__DIR__ . '/../data/data.json'), true);

        unset($dataFromJson['score']);
        unset($dataFromJson['solverStatus']);

        $this->assertEquals($dataFromJson, $dataFromExcel);
        // ignore score and solverStatus
    }

}