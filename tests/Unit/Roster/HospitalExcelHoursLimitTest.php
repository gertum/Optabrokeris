<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Hospital\ExcelWrapper;
use App\Domain\Roster\Hospital\WorkingHoursTitle;
use Tests\TestCase;

class HospitalExcelHoursLimitTest extends TestCase
{
    /**
     * @dataProvider provideExcelFiles
     */
    public function testParseExcel(string $file, WorkingHoursTitle $expectedWorkingHoursTitle)
    {
        $wrapper = ExcelWrapper::parse($file);

        $workingHoursTitle = $wrapper->findWorkingHoursTitle();

        $this->assertEquals($expectedWorkingHoursTitle, $workingHoursTitle);
    }

    public static function provideExcelFiles(): array
    {
        return [
            'test vasaris' => [
                'file' => __DIR__ . '/data/vasaris.xlsx',
                '$expectedWorkingHoursTitle' => (new WorkingHoursTitle())->setRow(8)->setColumn(38),
            ],
            'test sausis' => [
                'file' => __DIR__ . '/data/sausis.xlsx',
                '$expectedWorkingHoursTitle' => (new WorkingHoursTitle())->setRow(8)->setColumn(38),
            ],
            'test birzelis' => [
                'file' => __DIR__ . '/data/birželis.xlsx',
                '$expectedWorkingHoursTitle' => (new WorkingHoursTitle())->setRow(8)->setColumn(39),
            ],
            'test birzelis bad' => [
                'file' => __DIR__ . '/data/birželis_bad.xlsx',
                '$expectedWorkingHoursTitle' => (new WorkingHoursTitle())->setRow(8)->setColumn(38),
            ],
            'test rugpjutis' => [
                'file' => __DIR__ . '/data/rugpjūtis.xlsx',
                '$expectedWorkingHoursTitle' => (new WorkingHoursTitle())->setRow(8)->setColumn(38),
            ],
            'test gruodis' => [
                'file' => __DIR__ . '/data/gruodis.xlsx',
                '$expectedWorkingHoursTitle' => (new WorkingHoursTitle())->setRow(8)->setColumn(38),
            ],
        ];
    }

}