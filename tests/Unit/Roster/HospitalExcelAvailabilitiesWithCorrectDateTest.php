<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Hospital\ExcelWrapper;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use DateTimeInterface;

class HospitalExcelAvailabilitiesWithCorrectDateTest extends TestCase
{

    /**
     * @dataProvider provideExcelsAndExpectations
     */
    public function testGetAvailabilities(string $file, int $testedEmployeeNumber, int $testedNumber, DateTimeInterface $expectedDate, string $expectedAvailability) {

        $wrapper = ExcelWrapper::parse($file);

        $eilNrTitle = $wrapper->findEilNrTitle();
        $eilNrs = $wrapper->parseEilNrs($eilNrTitle);
        $employees = $wrapper->parseEmployees($eilNrs);

        $dateRecognizer = $wrapper->findYearMonth();

        $availabilities = $wrapper->parseAvailabilities($eilNrs, $employees, $dateRecognizer->getYear(), $dateRecognizer->getMonth() );

        $availability = $availabilities[$testedEmployeeNumber][$testedNumber];

        $this->assertEquals( $expectedDate, $availability->date );
        $this->assertEquals($expectedAvailability, $availability->availabilityType );
    }

    public static function provideExcelsAndExpectations() : array {
        return [
            'test vasaris 1 employee' => [
                'file' => __DIR__ . '/data/vasaris.xlsx',
                'testedEmployeeNumber' => 1,
                'testedNumber' => 0,
                'expectedDate' => Carbon::create(2024, 2, 1),
                'expectedAvailability' => Availability::UNAVAILABLE,
            ],
            'test birželis 1 employee' => [
                'file' => __DIR__ . '/data/birželis.xlsx',
                'testedEmployeeNumber' => 2,
                'testedNumber' => 8,
                'expectedDate' => Carbon::create(2024, 6, 9),
                'expectedAvailability' => Availability::UNAVAILABLE,
            ],
        ];
    }
}