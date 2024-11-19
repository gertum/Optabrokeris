<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Employee;
use App\Domain\Roster\Hospital\ScheduleParser;
use App\Domain\Roster\Profile;
use App\Domain\Roster\Shift;
use PHPUnit\Framework\TestCase;

class PreferedScheduleParseTest extends TestCase
{

    /**
     * @dataProvider provideFiles
     * @param Availability[] $expectedAvailabilities
     */
    public function testParse(
        string   $file,
        Profile  $profile,
        int      $checkEmployeeIndex,
        int      $checkShiftIndex,
        Employee $expectedEmployee,
        Shift    $expectedShift,
        array    $checkAvailabilityIndexes,
        array    $expectedAvailabilities
    )
    {
        $scheduleParser = new ScheduleParser();

        $schedule = $scheduleParser->parsePreferedScheduleXls($file, $profile);

        $this->assertEquals($expectedEmployee, $schedule->employeeList[$checkEmployeeIndex]);
        $this->assertEquals($expectedShift, $schedule->shiftList[$checkShiftIndex]);

        for ($i = 0; $i < count($checkAvailabilityIndexes); $i++) {
            $checkAvailabilityIndex = $checkAvailabilityIndexes[$i];
            $expectedAvailability = $expectedAvailabilities[$i];
            $this->assertEquals($expectedAvailability->date, $schedule->availabilityList[$checkAvailabilityIndex]->date);
            $this->assertEquals($expectedAvailability->dateTill, $schedule->availabilityList[$checkAvailabilityIndex]->dateTill);
            $this->assertEquals($expectedAvailability->availabilityType, $schedule->availabilityList[$checkAvailabilityIndex]->availabilityType);
            $this->assertEquals($expectedAvailability->employee, $schedule->availabilityList[$checkAvailabilityIndex]->employee);
        }
    }

    public static function provideFiles(): array
    {
        return [
//            'test1' => [
//                'file' => __DIR__ . '/data/VULSK SPS budėjimų pageidavimai.xlsx',
//                'profile' => (new Profile())->setShiftBounds([8, 20]),
//                'checkEmployeeIndex' => 0,
//                'checkShiftIndex' => 0,
//                'expectedEmployee' => (new Employee())
//                    ->setName('Aleksandras Briedis')
//                    ->setRow(2)
//                    ->setExcelRow(3),
//                'expectedShift' => (new Shift())
//                    ->setId(1)
//                    ->setStart('2022-12-01T08:00:00')
//                    ->setEnd('2022-12-01T20:00:00')
//                ,
//                'checkAvailabilityIndexes' => [0],
//                'expectedAvailabilities' => [
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
//                    ]
//            ],
//            'test2' => [
//                'file' => __DIR__ . '/data/VULSK SPS budėjimų pageidavimai.xlsx',
//                'profile' => (new Profile())
//                    ->setShiftBounds([8, 20])
//                ,
//                'checkEmployeeIndex' => 30,
//                'checkShiftIndex' => 1,
//                'expectedEmployee' => (new Employee())
//                    ->setName('Linas Rinkūnas')
//                    ->setRow(32)
//                    ->setExcelRow(33),
//                'expectedShift' => (new Shift())
//                    ->setId(2)
//                    ->setStart('2022-12-01T20:00:00')
//                    ->setEnd('2022-12-02T08:00:00'),
//                'checkAvailabilityIndexes' => [],
//                'expectedAvailabilities' => []
//            ],
//            'test3 medium far availability ' => [
//                'file' => __DIR__ . '/data/VULSK SPS budėjimų pageidavimai.xlsx',
//                'profile' => (new Profile())
//                    ->setShiftBounds([8, 20])
//                ,
//                'checkEmployeeIndex' => 30,
//                'checkShiftIndex' => 1,
//                'expectedEmployee' => (new Employee())
//                    ->setName('Linas Rinkūnas')
//                    ->setRow(32)
//                    ->setExcelRow(33),
//                'expectedShift' => (new Shift())
//                    ->setId(2)
//                    ->setStart('2022-12-01T20:00:00')
//                    ->setEnd('2022-12-02T08:00:00'),
//
//                'checkAvailabilityIndexes' => [60, 61, 62, 63],
//                'expectedAvailabilities' => [
//                    (new Availability())
//                        ->setDate('2022-12-30T20:00:00')
//                        ->setDateTill('2022-12-31T08:00:00')
//                        ->setAvailabilityType(Availability::UNDESIRED)
//                        ->setEmployee(
//                            (new Employee())
//                                ->setName('Aleksandras Briedis')
//                                ->setRow(2)
//                                ->setExcelRow(3)
//                        ),
//                    (new Availability())
//                        ->setDate('2022-12-31T08:00:00')
//                        ->setDateTill('2022-12-31T20:00:00')
//                        ->setAvailabilityType(Availability::DESIRED)
//                        ->setEmployee(
//                            (new Employee())
//                                ->setName('Aleksandras Briedis')
//                                ->setRow(2)
//                                ->setExcelRow(3)
//                        ),
//                    (new Availability())
//                        ->setDate('2022-12-31T20:00:00')
//                        ->setDateTill('2023-01-01T08:00:00')
//                        ->setAvailabilityType(Availability::DESIRED)
//                        ->setEmployee(
//                            (new Employee())
//                                ->setName('Aleksandras Briedis')
//                                ->setRow(2)
//                                ->setExcelRow(3)
//                        ),
//                    (new Availability())
//                        ->setDate('2022-11-30T20:00:00')
//                        ->setDateTill('2022-12-01T08:00:00')
//                        ->setAvailabilityType(Availability::UNAVAILABLE)
//                        ->setEmployee(
//                            (new Employee())
//                                ->setName('Andrius Černauskas')
//                                ->setRow(3)
//                                ->setExcelRow(4)
//                        ),
//                ]
//            ],
//            'test3 far availability ' => [
//                'file' => __DIR__ . '/data/VULSK SPS budėjimų pageidavimai.xlsx',
//                'profile' => (new Profile())
//                    ->setShiftBounds([8, 20])
//                ,
//                'checkEmployeeIndex' => 30,
//                'checkShiftIndex' => 1,
//                'expectedEmployee' => (new Employee())
//                    ->setName('Linas Rinkūnas')
//                    ->setRow(32)
//                    ->setExcelRow(33),
//                'expectedShift' => (new Shift())
//                    ->setId(2)
//                    ->setStart('2022-12-01T20:00:00')
//                    ->setEnd('2022-12-02T08:00:00'),
//                'checkAvailabilityIndexes' => [
//                    // 25th (24 = 25-1, 63 availabilities for one row) employee , 30 day (30*2-1)
//                    (24*63)+59,
//                    // 23th (22 = 23-1, 63 availabilities for one row) employee , 10 day (10*2-1)
//                    (22*63)+19,
//                    // 23th (22 = 23-1, 63 availabilities for one row) employee , 10 day night (10*2-2)
//                    (22*63)+18,
//                    ],
//                'expectedAvailabilities' => [
//                    (new Availability())
//                    ->setDate('2022-12-30T08:00:00')
//                    ->setDateTill('2022-12-30T20:00:00')
//                    ->setAvailabilityType(Availability::DESIRED)
//                    ->setEmployee(
//                        (new Employee())
//                            ->setName('Beatričė Raščiūtė')
//                            ->setRow(26)
//                            ->setExcelRow(27)
//                    ),
//                    (new Availability())
//                    ->setDate('2022-12-10T08:00:00')
//                    ->setDateTill('2022-12-10T20:00:00')
//                    ->setAvailabilityType(Availability::UNDESIRED)
//                    ->setEmployee(
//                        (new Employee())
//                            ->setName('Aurelija Krikštaponienė')
//                            ->setRow(24)
//                            ->setExcelRow(25)
//                    ),
//                    (new Availability())
//                    ->setDate('2022-12-09T20:00:00')
//                    ->setDateTill('2022-12-10T08:00:00')
//                    ->setAvailabilityType(Availability::DESIRED)
//                    ->setEmployee(
//                        (new Employee())
//                            ->setName('Aurelija Krikštaponienė')
//                            ->setRow(24)
//                            ->setExcelRow(25)
//                    ),
//                ]
//            ],

            'test4 other file' => [
                'file' => __DIR__ . '/data/Clinic Roster Template kopija.xlsx',
                'profile' => (new Profile())
                    ->setShiftBounds([8, 20])
                ,
                'checkEmployeeIndex' => 29,
                'checkShiftIndex' => 1,
                'expectedEmployee' => (new Employee())
                    ->setName('Martynas Judickas')
                    ->setRow(30)
                    ->setExcelRow(31),
                'expectedShift' => (new Shift())
                    ->setId(2)
                    ->setStart('2022-12-01T20:00:00')
                    ->setEnd('2022-12-02T08:00:00'),
                'checkAvailabilityIndexes' => [
                    // 25th (24 = 25-1, 63 availabilities for one row) employee , 30 day (30*2-1)
                    (24*63)+59,
                    // 23th (22 = 23-1, 63 availabilities for one row) employee , 10 day (10*2-1)
                    (22*63)+19,
                    // 23th (22 = 23-1, 63 availabilities for one row) employee , 10 day night (10*2-2)
                    (22*63)+18,
                ],
                'expectedAvailabilities' => [
                    (new Availability())
                        ->setDate('2022-12-30T08:00:00')
                        ->setDateTill('2022-12-30T20:00:00')
                        ->setAvailabilityType(Availability::DESIRED)
                        ->setEmployee(
                            (new Employee())
                                ->setName('Beatričė Raščiūtė')
                                ->setRow(26)
                                ->setExcelRow(27)
                        ),
                    (new Availability())
                        ->setDate('2022-12-10T08:00:00')
                        ->setDateTill('2022-12-10T20:00:00')
                        ->setAvailabilityType(Availability::UNDESIRED)
                        ->setEmployee(
                            (new Employee())
                                ->setName('Aurelija Krikštaponienė')
                                ->setRow(24)
                                ->setExcelRow(25)
                        ),
                    (new Availability())
                        ->setDate('2022-12-09T20:00:00')
                        ->setDateTill('2022-12-10T08:00:00')
                        ->setAvailabilityType(Availability::DESIRED)
                        ->setEmployee(
                            (new Employee())
                                ->setName('Aurelija Krikštaponienė')
                                ->setRow(24)
                                ->setExcelRow(25)
                        ),
                ]
            ],
        ];
    }
}