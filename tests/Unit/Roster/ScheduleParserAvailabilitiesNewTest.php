<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Hospital\ScheduleParser;
use App\Domain\Roster\Hospital\ShiftsBuilder;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use DateTimeInterface;

class ScheduleParserAvailabilitiesNewTest extends TestCase
{
    /**
     * @dataProvider provideScheduleParserData
     */
    public function testScheduleParserForAvailabilities(
        string $file,
        string $testedAvailabilityDate,
        string $expectedEmployeeName,
        string $expectedAvailabilityType,
        DateTimeInterface $expectedAvailabilityDate
    ) {
        $scheduleParser = new ScheduleParser();

        $timeSlices = ShiftsBuilder::transformBoundsToTimeSlices([8,20]);
        $schedule = $scheduleParser->parseScheduleXlsNew($file, $timeSlices);
        $schedule->sortAvailabilities();
        $availability = $schedule->findAvailability($expectedEmployeeName, $testedAvailabilityDate, true);

        $this->assertEquals($expectedEmployeeName, $availability->employee->name);
        $this->assertEquals($expectedAvailabilityType, $availability->availabilityType);
        $this->assertEquals($expectedAvailabilityDate, $availability->date);
    }

    public static function provideScheduleParserData(): array
    {
        return [
            'test small 1' => [
                'file' => __DIR__ . '/data/preferences_solved_result.xlsx',
                'testedAvailabilityDate' => '2024-11-01',
                'expectedEmployeeName' => 'Aleksandras Briedis',
                'expectedAvailabilityType' => Availability::UNAVAILABLE,
                'expectedAvailabilityDate' => Carbon::create(2024, 11, 1)
            ],
        ];
    }

}