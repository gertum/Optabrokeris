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
        ];
    }
}