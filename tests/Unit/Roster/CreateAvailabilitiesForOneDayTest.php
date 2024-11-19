<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Hospital\ScheduleParser;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Spatie\DataTransferObject\DataTransferObject;

class CreateAvailabilitiesForOneDayTest extends TestCase
{
    /**
     * @param Availability[] $expectedAvailabilities
     * @dataProvider provideAvailabilityValue
     */
    public function testCreateAvailabilities(string $availabilityValue, Carbon $currentDay, array $expectedAvailabilities) {
        $scheduleParser = new ScheduleParser();
        $availabilities = $scheduleParser->createAvailabilitiesForOneDay($availabilityValue, $currentDay);

        $this->assertEquals($expectedAvailabilities, $availabilities);
    }

    public static function provideAvailabilityValue() : array {
        return [
            'test1' => [
                'availabilityValue' => '',
                'currentDay' => Carbon::create(2022, 12, 2),
                'expectedAvailabilities' => [
                    '2022-12-01T20:00:00' => (new Availability())
                        ->setDate('2022-12-01T20:00:00')
                        ->setDateTill('2022-12-02T08:00:00')
                        ->setAvailabilityType(Availability::AVAILABLE),
                    '2022-12-02T08:00:00' => (new Availability())
                        ->setDate('2022-12-02T08:00:00')
                        ->setDateTill('2022-12-02T20:00:00')
                        ->setAvailabilityType(Availability::AVAILABLE),
                ]
            ],
            'test2' => [
                'availabilityValue' => 'D',
                'currentDay' => Carbon::create(2022, 12, 2),
                'expectedAvailabilities' => [
                    '2022-12-01T20:00:00' => (new Availability())
                        ->setDate('2022-12-01T20:00:00')
                        ->setDateTill('2022-12-02T08:00:00')
                        ->setAvailabilityType(Availability::UNDESIRED),
                    '2022-12-02T08:00:00' => (new Availability())
                        ->setDate('2022-12-02T08:00:00')
                        ->setDateTill('2022-12-02T20:00:00')
                        ->setAvailabilityType(Availability::DESIRED),
                ]
            ],
            'test3 P' => [
                'availabilityValue' => 'P',
                'currentDay' => Carbon::create(2022, 12, 2),
                'expectedAvailabilities' => [
                    '2022-12-01T20:00:00' => (new Availability())
                        ->setDate('2022-12-01T20:00:00')
                        ->setDateTill('2022-12-02T08:00:00')
                        ->setAvailabilityType(Availability::DESIRED),
                    '2022-12-02T08:00:00' => (new Availability())
                        ->setDate('2022-12-02T08:00:00')
                        ->setDateTill('2022-12-02T20:00:00')
                        ->setAvailabilityType(Availability::DESIRED),
                ]
            ],
            'test4 8-8' => [
                'availabilityValue' => '8-8',
                'currentDay' => Carbon::create(2022, 12, 2),
                'expectedAvailabilities' => [
                    '2022-12-02T08:00:00' => (new Availability())
                        ->setDate('2022-12-02T08:00:00')
                        ->setDateTill('2022-12-02T20:00:00')
                        ->setAvailabilityType(Availability::DESIRED),
                    '2022-12-02T20:00:00' => (new Availability())
                        ->setDate('2022-12-02T20:00:00')
                        ->setDateTill('2022-12-03T08:00:00')
                        ->setAvailabilityType(Availability::DESIRED),
                ]
            ],
            'test5 n' => [
                'availabilityValue' => 'n',
                'currentDay' => Carbon::create(2022, 12, 1),
                'expectedAvailabilities' => [
                    '2022-11-30T20:00:00' => (new Availability())
                        ->setDate('2022-11-30T20:00:00')
                        ->setDateTill('2022-12-01T08:00:00')
                        ->setAvailabilityType(Availability::DESIRED),
                    '2022-12-01T08:00:00' => (new Availability())
                        ->setDate('2022-12-01T08:00:00')
                        ->setDateTill('2022-12-01T20:00:00')
                        ->setAvailabilityType(Availability::UNDESIRED),
                ]
            ],
            'test6 x' => [
                'availabilityValue' => 'x',
                'currentDay' => Carbon::create(2022, 12, 1),
                'expectedAvailabilities' => [
                    '2022-11-30T20:00:00' => (new Availability())
                        ->setDate('2022-11-30T20:00:00')
                        ->setDateTill('2022-12-01T08:00:00')
                        ->setAvailabilityType(Availability::UNAVAILABLE),
                    '2022-12-01T08:00:00' => (new Availability())
                        ->setDate('2022-12-01T08:00:00')
                        ->setDateTill('2022-12-01T20:00:00')
                        ->setAvailabilityType(Availability::UNAVAILABLE),
                ]
            ],

            'test7 xN' => [
                'availabilityValue' => 'xN',
                'currentDay' => Carbon::create(2022, 12, 1),
                'expectedAvailabilities' => [
                    '2022-11-30T20:00:00' => (new Availability())
                        ->setDate('2022-11-30T20:00:00')
                        ->setDateTill('2022-12-01T08:00:00')
                        ->setAvailabilityType(Availability::UNAVAILABLE),
                    '2022-12-01T08:00:00' => (new Availability())
                        ->setDate('2022-12-01T08:00:00')
                        ->setDateTill('2022-12-01T20:00:00')
                        ->setAvailabilityType(Availability::AVAILABLE),
                ]
            ],
            'test7 XD' => [
                'availabilityValue' => 'XD',
                'currentDay' => Carbon::create(2022, 12, 1),
                'expectedAvailabilities' => [
                    '2022-11-30T20:00:00' => (new Availability())
                        ->setDate('2022-11-30T20:00:00')
                        ->setDateTill('2022-12-01T08:00:00')
                        ->setAvailabilityType(Availability::AVAILABLE),
                    '2022-12-01T08:00:00' => (new Availability())
                        ->setDate('2022-12-01T08:00:00')
                        ->setDateTill('2022-12-01T20:00:00')
                        ->setAvailabilityType(Availability::UNAVAILABLE),
                ]
            ],
        ];
    }
}