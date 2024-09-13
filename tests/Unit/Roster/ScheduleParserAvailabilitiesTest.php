<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Hospital\ScheduleParser;
use Carbon\Carbon;
use DateTimeInterface;
use Tests\TestCase;

class ScheduleParserAvailabilitiesTest extends TestCase
{

    /**
     * @dataProvider provideScheduleParserData
     */
    public function testScheduleParserForAvailabilities(
        string $file,
        int $expectedAvailabilitiesCount,
        int $testedAvailabilityIndex,
        string $expectedEmployeeName,
        string $expectedAvailabilityType,
        DateTimeInterface $expectedAvailabilityDate
    ) {
        $scheduleParser = new ScheduleParser();

        $schedule = $scheduleParser->parseScheduleXls($file, ScheduleParser::createHospitalTimeSlices());

        $this->assertCount($expectedAvailabilitiesCount, $schedule->availabilityList);

        $availability = $schedule->availabilityList[$testedAvailabilityIndex];

        $this->assertEquals($expectedEmployeeName, $availability->employee->name);
        $this->assertEquals($expectedAvailabilityType, $availability->availabilityType);
        $this->assertEquals($expectedAvailabilityDate, $availability->date);
    }

    public static function provideScheduleParserData(): array
    {
        return [
            'test small 1' => [
                'file' => __DIR__ . '/data/small.xlsx',
                'expectedAvailabilitiesCount' => 12,
                'testedAvailabilityIndex' => 6,
                'expectedEmployeeName' => 'Aleksandras Briedis 24/12',
                'expectedAvailabilityType' => Availability::AVAILABLE,
                'expectedAvailabilityDate' => Carbon::create(2024, 06, 01)
            ],
            'test small 2' => [
                'file' => __DIR__ . '/data/small.xlsx',
                'expectedAvailabilitiesCount' => 12,
                'testedAvailabilityIndex' => 7,
                'expectedEmployeeName' => 'Aleksandras Briedis 24/12',
                'expectedAvailabilityType' => Availability::AVAILABLE,
                'expectedAvailabilityDate' => Carbon::create(2024, 06, 02)
            ],
            'test small 3' => [
                'file' => __DIR__ . '/data/small.xlsx',
                'expectedAvailabilitiesCount' => 12,
                'testedAvailabilityIndex' => 8,
                'expectedEmployeeName' => 'Aleksandras Briedis 24/12',
                'expectedAvailabilityType' => Availability::UNAVAILABLE,
                'expectedAvailabilityDate' => Carbon::create(2024, 06, 03)
            ],
            'test small 4' => [
                'file' => __DIR__ . '/data/small.xlsx',
                'expectedAvailabilitiesCount' => 12,
                'testedAvailabilityIndex' => 9,
                'expectedEmployeeName' => 'Aleksandras Briedis 24/12',
                'expectedAvailabilityType' => Availability::UNAVAILABLE,
                'expectedAvailabilityDate' => Carbon::create(2024, 06, 04)
            ],
            'test small 5' => [
                'file' => __DIR__ . '/data/small.xlsx',
                'expectedAvailabilitiesCount' => 12,
                'testedAvailabilityIndex' => 10,
                'expectedEmployeeName' => 'Aleksandras Briedis 24/12',
                'expectedAvailabilityType' => Availability::AVAILABLE,
                'expectedAvailabilityDate' => Carbon::create(2024, 06, 05)
            ],
            'test small 1 3' => [
                'file' => __DIR__ . '/data/small.xlsx',
                'expectedAvailabilitiesCount' => 12,
                'testedAvailabilityIndex' => 2,
                'expectedEmployeeName' => 'Renata Juknevičienė 29/12',
                'expectedAvailabilityType' => Availability::AVAILABLE,
                'expectedAvailabilityDate' => Carbon::create(2024, 06, 03)
            ],
            'test birželis' => [
                'file' => __DIR__ . '/data/birželis.xlsx',
                'expectedAvailabilitiesCount' => 630,
                'testedAvailabilityIndex' => 623,
                'expectedEmployeeName' => 'Rinkūnas',
                'expectedAvailabilityType' => Availability::UNAVAILABLE,
                'expectedAvailabilityDate' => Carbon::create(2024, 06, 24)
            ],
        ];
    }
}