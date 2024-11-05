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
        string       $file,
        Profile      $profile,
        int          $checkEmployeeIndex,
        int          $checkShiftIndex,
        int          $checkAvailabilityIndex,
        Employee     $expectedEmployee,
        Shift        $expectedShift,
        Availability $expectedAvailability
    )
    {
        $scheduleParser = new ScheduleParser();

        $schedule = $scheduleParser->parsePreferedScheduleXls($file, $profile);

        $this->assertEquals($expectedEmployee, $schedule->employeeList[$checkEmployeeIndex]);
        $this->assertEquals($expectedShift, $schedule->shiftList[$checkShiftIndex]);
//        $this->assertEquals($expectedAvailability, $schedule->availabilityList[$checkAvailabilityIndex]);
    }

    public static function provideFiles(): array
    {
        return [
            'test1' => [
                'file' => __DIR__ . '/data/VULSK SPS budėjimų pageidavimai.xlsx',
                'profile' => (new Profile())->setShiftBounds([8, 20]),
                'checkEmployeeIndex' => 0,
                'checkShiftIndex' => 0,
                'checkAvailabilityIndex' => 0,
                'expectedEmployee' => (new Employee())
                    ->setName('Aleksandras Briedis')
                    ->setRow(2)
                    ->setExcelRow(3),
                'expectedShift' => (new Shift())
                    ->setId(1)
                    ->setStart('2022-12-01T08:00:00')
                    ->setEnd('2022-12-01T20:00:00')
                ,
                'expectedAvailability' => new Availability()
            ],
            'test2' => [
                'file' => __DIR__ . '/data/VULSK SPS budėjimų pageidavimai.xlsx',
                'profile' => (new Profile())
                    ->setShiftBounds([8, 20])
                ,
                'checkEmployeeIndex' => 30,
                'checkShiftIndex' => 1,
                'checkAvailabilityIndex' => 0,
                'expectedEmployee' => (new Employee())
                    ->setName('Linas Rinkūnas')
                    ->setRow(32)
                    ->setExcelRow(33),
                'expectedShift' => (new Shift())
                    ->setId(2)
                    ->setStart('2022-12-01T20:00:00')
                    ->setEnd('2022-12-02T08:00:00'),
                'expectedAvailability' => new Availability()
            ]
        ];
    }
}