<?php

namespace Tests\Unit\School;

use App\Exceptions\ValidateException;
use App\Transformers\School\CellValidator;
use PHPUnit\Event\Code\Test;
use Tests\TestCase;

class InvalidCellDataValidatorTest   extends TestCase
{


    /**
     * @dataProvider provideCellData
     */
    public function testInvalid($data, $messagePattern) {
        $this->expectException(ValidateException::class);
        $this->expectExceptionMessageMatches($messagePattern);
        CellValidator::validateCells($data);
    }

    public static function provideCellData(): array {
        return [
           'test1' => [
               'data' => self::getData(__DIR__.'/data/invalid/data1.json'),
               'messagePattern' => '/roomList/'
           ]
        ];
    }

    public static function getData(string $file ) : array {
        return json_decode(file_get_contents($file), true);
    }
}