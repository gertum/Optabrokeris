<?php

namespace Tests\Unit\School;

use App\Transformers\School\SchoolDataTransformer;
use PHPUnit\Framework\TestCase;

class SchoolDataTransformerTest extends TestCase
{
    /**
     * @dataProvider provideExcelData
     */
    public function testTransformToJson(array $excelData, array $expectedJsonData)
    {
        $schoolDataTransformer = new SchoolDataTransformer();
        $jsonData = $schoolDataTransformer->excelToJson($excelData);

        $this->assertEquals($expectedJsonData, $jsonData);
    }

    public static function provideExcelData(): array
    {
        return [
            'test1' => [
                'excelData' => include __DIR__.'/data/excelData.php',
                'expectedJsonData' => include __DIR__.'/data/jsonData.php',
            ],
            // more tests TODO
        ];
    }
}