<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Hospital\ScheduleParser;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class CreateAvailabilitiesForMultipleDaysTest extends TestCase
{
    /**
     * @param string[] $values
     * @param Availability[] $expectedAvailabilities
     * @dataProvider provideValues
     */
    public function testMultiple(array $values, Carbon $startingDate, array $expectedAvailabilities) {
        $scheduleParser = new ScheduleParser();

        $availabilities = $scheduleParser->createAvailabilitiesForMultipleDay($values, $startingDate);

        $this->assertEquals($expectedAvailabilities, $availabilities);
    }

    public static function provideValues() : array {
        return [
            'test0' => [
                'values' => [],
                'startingDate' => Carbon::create(2024),
                'expectedAvailabilities' => [],
            ],
            'test1' => [
                'values' => [2=>'D'],
                'startingDate' => Carbon::create(2024,11, 1),
                'expectedAvailabilities' => [
                    (new Availability())
                        ->setDate('2024-11-01T20:00:00')
                        ->setDateTill('2024-11-02T08:00:00')
                        ->setAvailabilityType(Availability::UNDESIRED),
                    (new Availability())
                        ->setDate('2024-11-02T08:00:00')
                        ->setDateTill('2024-11-02T20:00:00')
                        ->setAvailabilityType(Availability::DESIRED),
                    (new Availability())
                        ->setDate('2024-11-02T20:00:00')
                        ->setDateTill('2024-11-03T08:00:00')
                        // parameter to fillGaps function
                        ->setAvailabilityType(Availability::UNDESIRED),
                ],
            ],
            'test2' => [
                'values' => [
                    2=>'D',
                    3=>''
                ],
                'startingDate' => Carbon::create(2024,11, 1),
                'expectedAvailabilities' => [
                    (new Availability())
                        ->setDate('2024-11-01T20:00:00')
                        ->setDateTill('2024-11-02T08:00:00')
                        ->setAvailabilityType(Availability::UNDESIRED),
                    (new Availability())
                        ->setDate('2024-11-02T08:00:00')
                        ->setDateTill('2024-11-02T20:00:00')
                        ->setAvailabilityType(Availability::DESIRED),
                    (new Availability())
                        ->setDate('2024-11-02T20:00:00')
                        ->setDateTill('2024-11-03T08:00:00')
                        ->setAvailabilityType(Availability::AVAILABLE),
                    (new Availability())
                        ->setDate('2024-11-03T08:00:00')
                        ->setDateTill('2024-11-03T20:00:00')
                        ->setAvailabilityType(Availability::AVAILABLE),
                    (new Availability())
                        ->setDate('2024-11-03T20:00:00')
                        ->setDateTill('2024-11-04T08:00:00')
                        ->setAvailabilityType(Availability::UNDESIRED),
                ],
            ],
            'test3 overlapping' => [
                'values' => [
                    2=>'8-8r.',
                    3=>''
                ],
                'startingDate' => Carbon::create(2024,11, 1),
                'expectedAvailabilities' => [
                    (new Availability())
                        ->setDate('2024-11-01T20:00:00')
                        ->setDateTill('2024-11-02T08:00:00')
                        ->setAvailabilityType(Availability::UNDESIRED),
                    (new Availability())
                        ->setDate('2024-11-02T08:00:00')
                        ->setDateTill('2024-11-02T20:00:00')
                        ->setAvailabilityType(Availability::DESIRED),
                    (new Availability())
                        ->setDate('2024-11-02T20:00:00')
                        ->setDateTill('2024-11-03T08:00:00')
                        ->setAvailabilityType(Availability::DESIRED),
                    (new Availability())
                        ->setDate('2024-11-03T08:00:00')
                        ->setDateTill('2024-11-03T20:00:00')
                        ->setAvailabilityType(Availability::AVAILABLE),
                    (new Availability())
                        ->setDate('2024-11-03T20:00:00')
                        ->setDateTill('2024-11-04T08:00:00')
                        ->setAvailabilityType(Availability::UNDESIRED),
                ],
            ],
            'test4 gap' => [
                'values' => [
                    2=>'D',
                    3=>'8-8r.'
                ],
                'startingDate' => Carbon::create(2024,11, 1),
                'expectedAvailabilities' => [
                    (new Availability())
                        ->setDate('2024-11-01T20:00:00')
                        ->setDateTill('2024-11-02T08:00:00')
                        ->setAvailabilityType(Availability::UNDESIRED),
                    (new Availability())
                        ->setDate('2024-11-02T08:00:00')
                        ->setDateTill('2024-11-02T20:00:00')
                        ->setAvailabilityType(Availability::DESIRED),
                    (new Availability())
                        ->setDate('2024-11-02T20:00:00')
                        ->setDateTill('2024-11-03T08:00:00')
                        ->setAvailabilityType(Availability::UNDESIRED),
                    (new Availability())
                        ->setDate('2024-11-03T08:00:00')
                        ->setDateTill('2024-11-03T20:00:00')
                        ->setAvailabilityType(Availability::DESIRED),
                    (new Availability())
                        ->setDate('2024-11-03T20:00:00')
                        ->setDateTill('2024-11-04T08:00:00')
                        ->setAvailabilityType(Availability::DESIRED),
                ],
            ],
        ];
    }
}