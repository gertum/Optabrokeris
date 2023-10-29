<?php

namespace Tests\Unit\School;

use App\Transformers\ExcelParser;
use Tests\TestCase;

class ExtractIdFromDataTest extends TestCase
{
    /**
     * @dataProvider getDataSamples
     */
    public function testExtract(?string $data, ?int $expectedId) {
        $id = ExcelParser::extractId($data);

        $this->assertEquals($expectedId, $id);
    }

    public static function getDataSamples() : array {
        return [
            'test1' => [
                'data' => '[1] MONDAY 08:30:00-09:30:00',
                'expectedId' => 1
            ],
            'testempty' => [
                'data' => '',
                'expectedId' => null
            ],
            'test2' => [
                'data' => '[2][1] MONDAY 08:30:00-09:30:00',
                'expectedId' => 2
            ],
            'test3' => [
                'data' => '2 [1] MONDAY 08:30:00-09:30:00',
                'expectedId' => null
            ],
            'test4' => [
                'data' => null,
                'expectedId' => null
            ],
        ];
    }

}