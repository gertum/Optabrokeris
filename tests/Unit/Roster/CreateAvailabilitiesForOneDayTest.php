<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Hospital\ScheduleParser;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

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
            ]
        ];
    }
}