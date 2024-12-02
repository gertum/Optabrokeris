<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Hospital\DataFileDetector;
use PHPUnit\Framework\TestCase;

class DetectFileTest extends TestCase
{
    /**
     * @dataProvider provideFiles
     */
    public function testDetect(string $file, ?string $expectedType) {
        $excelDetector = new DataFileDetector();

        $type = $excelDetector->detectExcelType($file);

        $this->assertEquals($expectedType, $type);
    }

    public static function provideFiles() : array {
        return [
            'availabilities' => [
                'file' => __DIR__.'/data/Clinic Roster Template kopija.xlsx',
                'expectedType' => DataFileDetector::TYPE_AVAILABILITIES_XLS,
            ],
            'schedule' => [
                'file' => __DIR__.'/data/vasaris.xlsx',
                'expectedType' => DataFileDetector::TYPE_SCHEDULE_XLS,
            ],
            'subjects' => [
                'file' => __DIR__.'/data/Copy of VULSK SPS budėjimų pageidavimai-3.xlsx',
                'expectedType' => DataFileDetector::TYPE_SUBJECTS_XLS,
            ],
            'unknown' => [
                'file' => __DIR__.'/data/random.xlsx',
                'expectedType' => null,
            ],
            // school nedarom kol kas
        ];
    }
}