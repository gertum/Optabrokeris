<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Hospital\PreferencesExcelWrapper;
use Tests\TestCase;

class PreferedScheduleYearMonthTest extends TestCase
{
    /**
     * @dataProvider provideFiles
     */
    public function testDetectYearMonth(string $file, int $expectedYear, int $expectedMonth) {

        $wrapper = new PreferencesExcelWrapper($file);

        $yearMonth = $wrapper->findYearMonth();

        $this->assertEquals($expectedYear, $yearMonth->getYear());
        $this->assertEquals($expectedMonth, $yearMonth->getMonth());
    }

    public static function provideFiles() : array {
        return [
            'test1' => [
                'file' => __DIR__.'/data/VULSK SPS budėjimų pageidavimai.xlsx.xlsx',
                'expectedYear' => 2022,
                'expectedMonth' => 12,
            ]
        ];
    }
}