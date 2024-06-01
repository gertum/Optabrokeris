<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Hospital\ExcelWrapper;
use Tests\TestCase;

class HospitalExcelEmployeeHourLimitTest extends TestCase
{
    /**
     * @dataProvider provideExcels
     */
    public function testEmployees(
        string $file,
        int $testedEmployeeNumber,
        float $expectedHoursLimit,
        string $expectedName
    ) {
        $wrapper = ExcelWrapper::parse($file);

        $eilNrTitle = $wrapper->findEilNrTitle();
        $eilNrs = $wrapper->parseEilNrs($eilNrTitle);
        $employees = $wrapper->parseEmployees($eilNrs);

        $employee = $employees[$testedEmployeeNumber];

        $this->assertEquals($expectedName, $employee->name);
        $this->assertEquals($expectedHoursLimit, $employee->getHoursLimit());
    }

    public static function provideExcels(): array
    {
        return [
            'test vasaris 1 employee' => [
                'file' => __DIR__ . '/data/vasaris.xlsx',
                'testedEmployeeNumber' => 0,
                'expectedHoursLimit' => 74,
                'expectedName' => 'Renata Juknevičienė 29/12',
            ],
        ];
    }
}