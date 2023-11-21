<?php

namespace Tests\Unit\School;

use App\Transformers\School\CellValidator;
use PHPUnit\Framework\TestCase;

class CellValidatorTest extends TestCase
{
    /**
     * @dataProvider provideShcoolData
     */
    public function testValidate($data)
    {
        $this->assertTrue(CellValidator::validateCells($data));
    }

    public static function provideShcoolData(): array
    {
        return [
            'test1' => [
                'data' => self::getData(__DIR__ . '/data/valid/data1.json'),
            ],
            'test2' => [
                'data' => self::getData(__DIR__ . '/data/valid/data2.json'),
            ]
        ];
    }

    public static function getData(string $file): array
    {
        return json_decode(file_get_contents($file), true);
    }
}