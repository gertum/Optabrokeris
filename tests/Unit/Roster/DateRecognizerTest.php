<?php

namespace Tests\Unit\Roster;

use App\Util\DateRecognizer;
use Tests\TestCase;

class DateRecognizerTest extends TestCase
{
    /**
     * @dataProvider provideYearMonthRepresentations
     */
    public function testRecognize(
        string $yearMonthRepresentation,
        bool $recognized,
        int $expectedYear,
        int $expectedMonth
    ) {
        $dateRecognizer = new DateRecognizer();
        $this->assertEquals($recognized, $dateRecognizer->recognizeMonthDate($yearMonthRepresentation));
        $this->assertEquals($expectedYear, $dateRecognizer->getYear());
        $this->assertEquals($expectedMonth, $dateRecognizer->getMonth());
    }

    public static function provideYearMonthRepresentations(): array
    {
        return [
            'test1' => [
                'yearMonthRepresentation' => '2024m. Vasario mėn.',
                'recognized' => true,
                'expectedYear' => 2024,
                'expectedMonth' => 2,
            ],
            'test 2' => [
                'yearMonthRepresentation' => 'asdfasdf 2024 m. Vasario mėn.asdfasdf',
                'recognized' => true,
                'expectedYear' => 2024,
                'expectedMonth' => 2,
            ],
            'test 3' => [
                'yearMonthRepresentation' => 'asdfasdf 2025 m. gruodžio mėn.asdfasdf',
                'recognized' => true,
                'expectedYear' => 2025,
                'expectedMonth' => 12,
            ],
            'test 4' => [
                'yearMonthRepresentation' => 'asdfasdf 2025 m. balalaika mėn.asdfasdf',
                'recognized' => false,
                'expectedYear' => 0,
                'expectedMonth' => 0,
            ],
        ];
    }
}