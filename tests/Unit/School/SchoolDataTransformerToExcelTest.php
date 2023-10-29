<?php

namespace Tests\Unit\School;

use App\Transformers\School\SchoolDataTransformer;
use PHPUnit\Framework\TestCase;

class SchoolDataTransformerToExcelTest extends TestCase
{
    /**
     * @dataProvider provideJsonData
     */
    public function testTransform(array $jsonData, array $expectedExcelData)
    {
        $schoolDataTransformer = new SchoolDataTransformer();
        $excelData = $schoolDataTransformer->jsonToExcel($jsonData);

        $this->assertEquals($expectedExcelData, $excelData);
    }

    public static function provideJsonData(): array
    {
        return [
            'test1' => [
                'jsonData' => include __DIR__ . '/data/jsonData.php',
                'expectedExcelData' => include __DIR__ . '/data/excelData.php'
            ]
        ];
    }

}