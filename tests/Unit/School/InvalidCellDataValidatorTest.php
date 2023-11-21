<?php

namespace Tests\Unit\School;

use App\Exceptions\ValidateException;
use App\Transformers\School\CellValidator;
use PHPUnit\Event\Code\Test;
use Tests\TestCase;

class InvalidCellDataValidatorTest extends TestCase
{


    /**
     * @dataProvider provideCellData
     */
    public function testInvalid($data, $messagePattern)
    {
        $this->expectException(ValidateException::class);
        $this->expectExceptionMessageMatches($messagePattern);
        CellValidator::validateCells($data);
    }

    public static function provideCellData(): array
    {
        return [
            'test1' => [
                'data' => self::getData(__DIR__ . '/data/invalid/data1.json'),
                'messagePattern' => '/roomList/'
            ],
            'test2_dayOfWeek' => [
                'data' => self::getData(__DIR__ . '/data/invalid/data2_dayOfWeek.json'),
                'messagePattern' => '/MONDAY2/'
            ],
            'test2_timeslotId' => [
                'data' => self::getData(__DIR__ . '/data/invalid/data2_timeslotId.json'),
                'messagePattern' => '/timeslot id/'
            ],
            'test2_startTime' => [
                'data' => self::getData(__DIR__ . '/data/invalid/data2_startTime.json'),
                'messagePattern' => '/timeslot start time/'
            ],
            'test2_endTime' => [
                'data' => self::getData(__DIR__ . '/data/invalid/data2_endTime.json'),
                'messagePattern' => '/timeslot end time/'
            ],
            'test2_roomId' => [
                'data' => self::getData(__DIR__ . '/data/invalid/data2_roomId.json'),
                'messagePattern' => '/room id/'
            ],
            'test2_lessonId' => [
                'data' => self::getData(__DIR__ . '/data/invalid/data2_lessonId.json'),
                'messagePattern' => '/lesson id/'
            ]

            // TODO duplicates of ids
            // TODO assigned timeslot and room structure
        ];
    }

    public static function getData(string $file): array
    {
        return json_decode(file_get_contents($file), true);
    }
}