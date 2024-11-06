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
        $this->assertEquals($expectedAvailability->date, $schedule->availabilityList[$checkAvailabilityIndex]->date);
        $this->assertEquals($expectedAvailability->dateTill, $schedule->availabilityList[$checkAvailabilityIndex]->dateTill);
        $this->assertEquals($expectedAvailability->availabilityType, $schedule->availabilityList[$checkAvailabilityIndex]->availabilityType);
        $this->assertEquals($expectedAvailability->employee, $schedule->availabilityList[$checkAvailabilityIndex]->employee);
    }

    public static function provideFiles(): array
    {
        return [
//            'test1' => [
//                'file' => __DIR__ . '/data/VULSK SPS budėjimų pageidavimai.xlsx',
//                'profile' => (new Profile())->setShiftBounds([8, 20]),
//                'checkEmployeeIndex' => 0,
//                'checkShiftIndex' => 0,
//                'checkAvailabilityIndex' => 0,
//                'expectedEmployee' => (new Employee())
//                    ->setName('Aleksandras Briedis')
//                    ->setRow(2)
//                    ->setExcelRow(3),
//                'expectedShift' => (new Shift())
//                    ->setId(1)
//                    ->setStart('2022-12-01T08:00:00')
//                    ->setEnd('2022-12-01T20:00:00')
//                ,
//                'expectedAvailability' =>
//                    (new Availability())
//                        ->setDate('2022-11-30T20:00:00')
//                        ->setDateTill('2022-12-01T08:00:00')
//                        ->setAvailabilityType(Availability::AVAILABLE)
//                        ->setEmployee(
//                            (new Employee())
//                                ->setName('Aleksandras Briedis')
//                                ->setRow(2)
//                                ->setExcelRow(3)
//                        )
//            ],
//            'test2' => [
//                'file' => __DIR__ . '/data/VULSK SPS budėjimų pageidavimai.xlsx',
//                'profile' => (new Profile())
//                    ->setShiftBounds([8, 20])
//                ,
//                'checkEmployeeIndex' => 30,
//                'checkShiftIndex' => 1,
//                'checkAvailabilityIndex' => 0,
//                'expectedEmployee' => (new Employee())
//                    ->setName('Linas Rinkūnas')
//                    ->setRow(32)
//                    ->setExcelRow(33),
//                'expectedShift' => (new Shift())
//                    ->setId(2)
//                    ->setStart('2022-12-01T20:00:00')
//                    ->setEnd('2022-12-02T08:00:00'),
//                'expectedAvailability' => (new Availability())
//                    ->setDate('2022-11-30T20:00:00')
//                    ->setDateTill('2022-12-01T08:00:00')
//                    ->setAvailabilityType(Availability::AVAILABLE)
//                    ->setEmployee(
//                        (new Employee())
//                            ->setName('Aleksandras Briedis')
//                            ->setRow(2)
//                            ->setExcelRow(3)
//                    )
//            ],
            'test3 medium far availability ' => [
                'file' => __DIR__ . '/data/VULSK SPS budėjimų pageidavimai.xlsx',
                'profile' => (new Profile())
                    ->setShiftBounds([8, 20])
                ,
                'checkEmployeeIndex' => 30,
                'checkShiftIndex' => 1,
                'checkAvailabilityIndex' => 61,
                'expectedEmployee' => (new Employee())
                    ->setName('Linas Rinkūnas')
                    ->setRow(32)
                    ->setExcelRow(33),
                'expectedShift' => (new Shift())
                    ->setId(2)
                    ->setStart('2022-12-01T20:00:00')
                    ->setEnd('2022-12-02T08:00:00'),


                'expectedAvailability' => (new Availability())
                    ->setDate('2022-12-31T08:00:00')
                    ->setDateTill('2022-12-31T20:00:00')
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee(
                        (new Employee())
                            ->setName('Aleksandras Briedis')
                            ->setRow(2)
                            ->setExcelRow(3)
                    )
            ],
//            'test3 far availability ' => [
//                'file' => __DIR__ . '/data/VULSK SPS budėjimų pageidavimai.xlsx',
//                'profile' => (new Profile())
//                    ->setShiftBounds([8, 20])
//                ,
//                'checkEmployeeIndex' => 30,
//                'checkShiftIndex' => 1,
//                'checkAvailabilityIndex' => (27*31+30)*2, // 25th employee , 30 day, 2 availabilities for each day
//                'expectedEmployee' => (new Employee())
//                    ->setName('Linas Rinkūnas')
//                    ->setRow(32)
//                    ->setExcelRow(33),
//                'expectedShift' => (new Shift())
//                    ->setId(2)
//                    ->setStart('2022-12-01T20:00:00')
//                    ->setEnd('2022-12-02T08:00:00'),
//                'expectedAvailability' => (new Availability())
//                    ->setDate('2022-12-30T08:00:00')
//                    ->setDateTill('2022-12-30T20:00:00')
//                    ->setAvailabilityType(Availability::DESIRED)
//                    ->setEmployee(
//                        (new Employee())
//                            ->setName('Beatričė Raščiūtė')
//                            ->setRow(26)
//                            ->setExcelRow(27)
//                    )
//            ],
        ];
    }
}