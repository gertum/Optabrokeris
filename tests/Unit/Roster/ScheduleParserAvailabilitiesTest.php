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
            'test small' => [
                'file' => __DIR__ . '/data/small.xlsx',
                'expectedAvailabilitiesCount' => 12,
                'testedAvailabilityIndex' => 6,
                'expectedEmployeeName' => 'Aleksandras Briedis 24/12',
                'expectedAvailabilityType' => Availability::DESIRED,
                'expectedAvailabilityDate' => Carbon::create(2024, 06, 01)
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