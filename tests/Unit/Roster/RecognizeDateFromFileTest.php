<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Hospital\ExcelWrapper;
use PHPUnit\Framework\TestCase;

class RecognizeDateFromFileTest extends TestCase
{
    /**
     * @dataProvider provideFilesForRecognition
     */
    public function testRecognizeDate(string $file, int $expectedYear, int $expectedMonth)
    {
        $wrapper = ExcelWrapper::parse($file);

        $dateRecognizer = $wrapper->findYearMonth();

        $this->assertEquals($expectedYear, $dateRecognizer->getYear());
        $this->assertEquals($expectedMonth, $dateRecognizer->getMonth());
    }

    public static function provideFilesForRecognition(): array
    {
        return [
            'test vasaris' => [
                'file' => __DIR__ . '/data/vasaris.xlsx',
                'expectedYear' => 2024,
                'expectedMonth' => 2
            ],
            'test sausis' => [
                'file' => __DIR__ . '/data/sausis.xlsx',
                'expectedYear' => 2024,
                'expectedMonth' => 1
            ],
            'test birzelis' => [
                'file' => __DIR__ . '/data/birželis.xlsx',
                'expectedYear' => 2024,
                'expectedMonth' => 6
            ],
            'test birzelis bad' => [
                'file' => __DIR__ . '/data/birželis_bad.xlsx',
                'expectedYear' => 0,
                'expectedMonth' => 0,
            ],
            'test rugpjutis' => [
                'file' => __DIR__ . '/data/rugpjūtis.xlsx',
                'expectedYear' => 2024,
                'expectedMonth' => 8
            ],
            'test gruodis' => [
                'file' => __DIR__ . '/data/gruodis.xlsx',
                'expectedYear' => 2024,
                'expectedMonth' => 12
            ],
        ];
    }
}