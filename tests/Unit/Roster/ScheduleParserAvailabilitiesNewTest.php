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
        // adapt slices to availabilities later?

        $schedule->sortAvailabilities();
        $availability = $schedule->findAvailability($expectedEmployeeName, $testedAvailabilityDate, true);

        $this->assertEquals($expectedEmployeeName, $availability->employee->name);
        $this->assertEquals($expectedAvailabilityType, $availability->availabilityType);
        $this->assertEquals($expectedAvailabilityDate, $availability->date);
    }

    public static function provideScheduleParserData(): array
    {
        return [
            'test 1' => [
                'file' => __DIR__ . '/data/preferences_solved_result.xlsx',
                'testedAvailabilityDate' => '2024-11-01',
                'expectedEmployeeName' => 'Aleksandras Briedis',
                'expectedAvailabilityType' => Availability::UNAVAILABLE,
                'expectedAvailabilityDate' => Carbon::create(2024, 11, 1)
            ],
            'test 2' => [
                'file' => __DIR__ . '/data/preferences_solved_result.xlsx',
                'testedAvailabilityDate' => '2024-11-03 11:00:00',
                'expectedEmployeeName' => 'Aleksandras Briedis',
                'expectedAvailabilityType' => Availability::AVAILABLE,
                'expectedAvailabilityDate' => Carbon::create(2024, 11, 3, 0)
            ],
            'test 3' => [
                'file' => __DIR__ . '/data/preferences_solved_result.xlsx',
                'testedAvailabilityDate' => '2024-11-03 21:00:00',
                'expectedEmployeeName' => 'Aleksandras Briedis',
                'expectedAvailabilityType' => Availability::UNAVAILABLE,
                'expectedAvailabilityDate' => Carbon::create(2024, 11, 3, 12)
            ],
            'test 4' => [
                'file' => __DIR__ . '/data/preferences_solved_result.xlsx',
                'testedAvailabilityDate' => '2024-11-05 00:00:00',
                'expectedEmployeeName' => 'Linas Rinkūnas',
                'expectedAvailabilityType' => Availability::DESIRED,
                'expectedAvailabilityDate' => Carbon::create(2024, 11, 5, 0)
            ],

            'test 5' => [
                'file' => __DIR__ . '/data/preferences_solved_result.xlsx',
                'testedAvailabilityDate' => '2024-11-05 21:00:00',
                'expectedEmployeeName' => 'Linas Rinkūnas',
                'expectedAvailabilityType' => Availability::DESIRED,
                'expectedAvailabilityDate' => Carbon::create(2024, 11, 5, 12)
            ],

            'test 6' => [
                'file' => __DIR__ . '/data/preferences_solved_result.xlsx',
                'testedAvailabilityDate' => '2024-11-07 00:00:00',
                'expectedEmployeeName' => 'Linas Rinkūnas',
                'expectedAvailabilityType' => Availability::DESIRED,
                'expectedAvailabilityDate' => Carbon::create(2024, 11, 7, 0)
            ],

            'test 7' => [
                'file' => __DIR__ . '/data/preferences_solved_result.xlsx',
                'testedAvailabilityDate' => '2024-11-07 21:00:00',
                'expectedEmployeeName' => 'Linas Rinkūnas',
                'expectedAvailabilityType' => Availability::AVAILABLE,
                'expectedAvailabilityDate' => Carbon::create(2024, 11, 7, 12)
            ],

            'test 8' => [
                'file' => __DIR__ . '/data/preferences_solved_result.xlsx',
                'testedAvailabilityDate' => '2024-11-05 00:00:00',
                'expectedEmployeeName' => 'Tomas Trybė',
                'expectedAvailabilityType' => Availability::UNAVAILABLE,
                'expectedAvailabilityDate' => Carbon::create(2024, 11, 5, 00)
            ],
        ];
    }

}