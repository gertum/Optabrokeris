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
                    '2024-11-01T20:00:00' => (new Availability())
                        ->setDate('2024-11-01T20:00:00')
                        ->setDateTill('2024-11-02T08:00:00')
                        ->setAvailabilityType(Availability::UNAVAILABLE),
                    '2024-11-02T08:00:00' => (new Availability())
                        ->setDate('2024-11-02T08:00:00')
                        ->setDateTill('2024-11-02T20:00:00')
                        ->setAvailabilityType(Availability::DESIRED)
                ],
            ],
            'test2' => [
                'values' => [
                    2=>'D',
                    3=>''
                ],
                'startingDate' => Carbon::create(2024,11, 1),
                'expectedAvailabilities' => [
                    '2024-11-01T20:00:00' => (new Availability())
                        ->setDate('2024-11-01T20:00:00')
                        ->setDateTill('2024-11-02T08:00:00')
                        ->setAvailabilityType(Availability::UNAVAILABLE),
                    '2024-11-02T08:00:00' => (new Availability())
                        ->setDate('2024-11-02T08:00:00')
                        ->setDateTill('2024-11-02T20:00:00')
                        ->setAvailabilityType(Availability::DESIRED),
                    '2024-11-02T20:00:00' => (new Availability())
                        ->setDate('2024-11-02T20:00:00')
                        ->setDateTill('2024-11-03T08:00:00')
                        ->setAvailabilityType(Availability::AVAILABLE),
                    '2024-11-03T08:00:00' => (new Availability())
                        ->setDate('2024-11-03T08:00:00')
                        ->setDateTill('2024-11-03T20:00:00')
                        ->setAvailabilityType(Availability::AVAILABLE),
                ],
            ],
            'test3 overlapping' => [
                'values' => [
                    2=>'8-8r.',
                    3=>''
                ],
                'startingDate' => Carbon::create(2024,11, 1),
                'expectedAvailabilities' => [
                    '2024-11-02T08:00:00' => (new Availability())
                        ->setDate('2024-11-02T08:00:00')
                        ->setDateTill('2024-11-02T20:00:00')
                        ->setAvailabilityType(Availability::DESIRED),
                    '2024-11-02T20:00:00' => (new Availability())
                        ->setDate('2024-11-02T20:00:00')
                        ->setDateTill('2024-11-03T08:00:00')
                        ->setAvailabilityType(Availability::DESIRED),
                    '2024-11-03T08:00:00' => (new Availability())
                        ->setDate('2024-11-03T08:00:00')
                        ->setDateTill('2024-11-03T20:00:00')
                        ->setAvailabilityType(Availability::AVAILABLE),
                ],
            ],
            'test4 gap' => [
                'values' => [
                    2=>'D',
                    3=>'8-8r.'
                ],
                'startingDate' => Carbon::create(2024,11, 1),
                'expectedAvailabilities' => [
                    '2024-11-01T20:00:00' => (new Availability())
                        ->setDate('2024-11-01T20:00:00')
                        ->setDateTill('2024-11-02T08:00:00')
                        ->setAvailabilityType(Availability::UNAVAILABLE),
                    '2024-11-02T08:00:00' => (new Availability())
                        ->setDate('2024-11-02T08:00:00')
                        ->setDateTill('2024-11-02T20:00:00')
                        ->setAvailabilityType(Availability::DESIRED),
                    // lets leave gaps, the gaps solution will be resolved in the solution microservice
//                    '2024-11-02T20:00:00' => (new Availability())
//                        ->setDate('2024-11-02T20:00:00')
//                        ->setDateTill('2024-11-03T08:00:00')
//                        ->setAvailabilityType(Availability::UNAVAILABLE),
                    '2024-11-03T08:00:00' => (new Availability())
                        ->setDate('2024-11-03T08:00:00')
                        ->setDateTill('2024-11-03T20:00:00')
                        ->setAvailabilityType(Availability::DESIRED),
                    '2024-11-03T20:00:00' => (new Availability())
                        ->setDate('2024-11-03T20:00:00')
                        ->setDateTill('2024-11-04T08:00:00')
                        ->setAvailabilityType(Availability::DESIRED),
                ],
            ],
        ];
    }
}