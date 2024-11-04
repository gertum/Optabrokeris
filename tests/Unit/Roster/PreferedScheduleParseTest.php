<?php

namespace Tests\Unit\Roster;

use App\Data\Profile;
use App\Domain\Roster\Availability;
use App\Domain\Roster\Employee;
use App\Domain\Roster\Hospital\ScheduleParser;
use App\Domain\Roster\Shift;
use PHPUnit\Framework\TestCase;

class PreferedScheduleParseTest extends TestCase
{

    /**
     * @dataProvider provideFiles
     */
    public function testParse(
        string $file,
        Profile $profile,
        int $checkEmployeeIndex,
        int $checkShiftIndex,
        int $checkAvailabilityIndex,
        Employee $expectedEmployee,
        Shift $expectedShift,
        Availability $expectedAvailability
    ) {
        $scheduleParser = new ScheduleParser();

        $schedule = $scheduleParser->parsePreferedScheduleXls($file, $profile);

        $this->assertEquals($expectedEmployee, $schedule->employeeList[$checkEmployeeIndex]);
        $this->assertEquals($expectedShift, $schedule->shiftList[$checkShiftIndex]);
        $this->assertEquals($expectedAvailability, $schedule->availabilityList[$checkAvailabilityIndex]);
    }

    public static function provideFiles() : array {
        return [
            'test1' => [
                'file' => __DIR__.'/data/VULSK SPS budėjimų pageidavimai.xlsx',
                'profile' => new Profile(),
                'checkEmployeeIndex' => 0,
                'checkShiftIndex' => 0,
                'checkAvailabilityIndex' => 0,
                'expectedEmployee' => new Employee(),
                'expectedShift' => new Shift(),
                'expectedAvailability' => new Availability()
            ]
        ];
    }
}