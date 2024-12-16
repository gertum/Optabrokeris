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
     *
     * @param array[] $checkAvailabilityParams
     * @param Availability[] $expectedAvailabilities
     */
    public function testParse(
        string $file,
        Profile $profile,
        int $checkEmployeeIndex,
        int $checkShiftIndex,
        Employee $expectedEmployee,
        Shift $expectedShift,
        array $checkAvailabilityParams,
        array $expectedAvailabilities
    ) {
        $scheduleParser = new ScheduleParser();
        $scheduleParser->setParserVersion(1);

        $schedule = $scheduleParser->parsePreferredScheduleXls($file, $profile);

        $this->assertGreaterThan($checkEmployeeIndex, count($schedule->employeeList));
        $this->assertEquals($expectedEmployee, $schedule->employeeList[$checkEmployeeIndex]);
        $this->assertEquals($expectedShift, $schedule->shiftList[$checkShiftIndex]);

        $schedule->assignEmployeesSequenceNumbers();
        $schedule->sortAvailabilities();

        for ($i = 0; $i < count($checkAvailabilityParams); $i++) {
            $checkAvailabilityParam = $checkAvailabilityParams[$i];
            $expectedAvailability = $expectedAvailabilities[$i];

            $foundAvailability = $schedule
                ->findAvailability($checkAvailabilityParam['name'], $checkAvailabilityParam['date'], true);
            $this->assertEquals($expectedAvailability->date, $foundAvailability->date);
            $this->assertEquals($expectedAvailability->dateTill, $foundAvailability->dateTill);
            $this->assertEquals($expectedAvailability->availabilityType, $foundAvailability->availabilityType);
            $this->assertEquals($expectedAvailability->employee, $foundAvailability->employee);
        }
    }

    public static function provideFiles(): array
    {
        return [
            'test1' => [
                'file' => __DIR__ . '/data/VULSK SPS budėjimų pageidavimai.xlsx',
                'profile' => (new Profile())->setShiftBounds([8, 20]),
                'checkEmployeeIndex' => 0,
                'checkShiftIndex' => 0,
                'expectedEmployee' => (new Employee())
                    ->setName('Aleksandras Briedis')
                    ->setRow(2)
                    ->setExcelRow(3)
                    ->setSequenceNumber(1)
                ,
                'expectedShift' => (new Shift())
                    ->setId(1)
                    ->setStart('2022-12-01T08:00:00')
                    ->setEnd('2022-12-01T20:00:00')
                ,
                'checkAvailabilityParams' => [
                    ['name' => 'Aleksandras Briedis', 'date' => '2022-11-30T20:00:00'],
                ],
                'expectedAvailabilities' => [
                    (new Availability())
                        ->setDate('2022-11-30T20:00:00')
                        ->setDateTill('2022-12-01T08:00:00')
                        ->setAvailabilityType(Availability::AVAILABLE)
                        ->setEmployee(
                            (new Employee())
                                ->setName('Aleksandras Briedis')
                                ->setRow(2)
                                ->setExcelRow(3)
                                ->setSequenceNumber(1)
                        )
                    ]
            ],
            'test2' => [
                'file' => __DIR__ . '/data/VULSK SPS budėjimų pageidavimai.xlsx',
                'profile' => (new Profile())
                    ->setShiftBounds([8, 20])
                ,
                'checkEmployeeIndex' => 30,
                'checkShiftIndex' => 1,
                'expectedEmployee' => (new Employee())
                    ->setName('Linas Rinkūnas')
                    ->setRow(32)
                    ->setExcelRow(33)
                    ->setSequenceNumber(31)
                ,
                'expectedShift' => (new Shift())
                    ->setId(2)
                    ->setStart('2022-12-01T20:00:00')
                    ->setEnd('2022-12-02T08:00:00'),
                'checkAvailabilityParams' => [],
                'expectedAvailabilities' => []
            ],
            'test3 medium far availability ' => [
                'file' => __DIR__ . '/data/VULSK SPS budėjimų pageidavimai.xlsx',
                'profile' => (new Profile())
                    ->setShiftBounds([8, 20])
                ,
                'checkEmployeeIndex' => 30,
                'checkShiftIndex' => 1,
                'expectedEmployee' => (new Employee())
                    ->setName('Linas Rinkūnas')
                    ->setRow(32)
                    ->setExcelRow(33)
                    ->setSequenceNumber(31)
                ,
                'expectedShift' => (new Shift())
                    ->setId(2)
                    ->setStart('2022-12-01T20:00:00')
                    ->setEnd('2022-12-02T08:00:00'),

                'checkAvailabilityParams' => [
                    ['name' => 'Aleksandras Briedis', 'date' => '2022-12-30T20:00:00'],
                    ['name' => 'Aleksandras Briedis', 'date' => '2022-12-31T08:00:00'],
                    ['name' => 'Aleksandras Briedis', 'date' => '2022-12-31T20:00:00'],
                    ['name' => 'Andrius Černauskas', 'date' => '2022-11-30T20:00:00'],
                ],
                'expectedAvailabilities' => [
                    (new Availability())
                        ->setDate('2022-12-30T20:00:00')
                        ->setDateTill('2022-12-31T08:00:00')
                        ->setAvailabilityType(Availability::UNDESIRED)
                        ->setEmployee(
                            (new Employee())
                                ->setName('Aleksandras Briedis')
                                ->setRow(2)
                                ->setExcelRow(3)
                                ->setSequenceNumber(1)
                        ),
                    (new Availability())
                        ->setDate('2022-12-31T08:00:00')
                        ->setDateTill('2022-12-31T20:00:00')
                        ->setAvailabilityType(Availability::DESIRED)
                        ->setEmployee(
                            (new Employee())
                                ->setName('Aleksandras Briedis')
                                ->setRow(2)
                                ->setExcelRow(3)
                                ->setSequenceNumber(1)
                        ),
                    (new Availability())
                        ->setDate('2022-12-31T20:00:00')
                        ->setDateTill('2023-01-01T08:00:00')
                        ->setAvailabilityType(Availability::DESIRED)
                        ->setEmployee(
                            (new Employee())
                                ->setName('Aleksandras Briedis')
                                ->setRow(2)
                                ->setExcelRow(3)
                                ->setSequenceNumber(1)
                        ),
                    (new Availability())
                        ->setDate('2022-11-30T20:00:00')
                        ->setDateTill('2022-12-01T08:00:00')
                        ->setAvailabilityType(Availability::UNAVAILABLE)
                        ->setEmployee(
                            (new Employee())
                                ->setName('Andrius Černauskas')
                                ->setRow(3)
                                ->setExcelRow(4)
                                ->setSequenceNumber(2)
                        ),
                ]
            ],
            'test3 far availability ' => [
                'file' => __DIR__ . '/data/VULSK SPS budėjimų pageidavimai.xlsx',
                'profile' => (new Profile())
                    ->setShiftBounds([8, 20])
                ,
                'checkEmployeeIndex' => 30,
                'checkShiftIndex' => 1,
                'expectedEmployee' => (new Employee())
                    ->setName('Linas Rinkūnas')
                    ->setRow(32)
                    ->setExcelRow(33)
                    ->setSequenceNumber(31)
                ,
                'expectedShift' => (new Shift())
                    ->setId(2)
                    ->setStart('2022-12-01T20:00:00')
                    ->setEnd('2022-12-02T08:00:00'),
                'checkAvailabilityParams' => [
                    ['name' => 'Beatričė Raščiūtė', 'date' => '2022-12-30T08:00:00'],
                    ['name' => 'Aurelija Krikštaponienė', 'date' => '2022-12-10T08:00:00'],
                    ['name' => 'Aurelija Krikštaponienė', 'date' => '2022-12-09T20:00:00'],
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
                            ->setSequenceNumber(25)
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
                            ->setSequenceNumber(23)
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
                            ->setSequenceNumber(23)
                    ),
                ]
            ],

            'test4 other file' => [
                'file' => __DIR__ . '/data/Clinic Roster Template kopija.xlsx',
                'profile' => (new Profile())
                    ->setShiftBounds([8, 20])
                ,
                'checkEmployeeIndex' => 26,
                'checkShiftIndex' => 1,
                'expectedEmployee' => (new Employee())
                    ->setName('Martynas Judickas')
                    ->setRow(30)
                    ->setExcelRow(31)
                    ->setSequenceNumber(27),
                'expectedShift' => (new Shift())
                    ->setId(2)
                    ->setStart('2024-11-01T20:00:00')
                    ->setEnd('2024-11-02T08:00:00'),
                'checkAvailabilityParams' => [
                    ['name' => 'Martynas Judickas', 'date' => '2024-11-04T20:00:00'],
                    ['name' => 'Martynas Judickas', 'date' => '2024-11-05T08:00:00'],
                    ['name' => 'Edgaras Baliūnas', 'date' => '2024-11-04T00:00:00'],
                    ['name' => 'Edgaras Baliūnas', 'date' => '2024-11-04T12:00:00'],
                    ['name' => 'Edgaras Baliūnas', 'date' => '2024-11-05T00:00:00'],
                    ['name' => 'Edgaras Baliūnas', 'date' => '2024-11-07T08:00:00'],

                    ['name' => 'Mantė Šmigelskaitė', 'date' => '2024-11-08T00:00:00'],
                    ['name' => 'Mantė Šmigelskaitė', 'date' => '2024-11-08T08:00:00'],
                ],
                'expectedAvailabilities' => [
                    (new Availability())
                        ->setDate('2024-11-04T20:00:00')
                        ->setDateTill('2024-11-05T08:00:00')
                        ->setAvailabilityType(Availability::AVAILABLE)
                        ->setEmployee(
                            (new Employee())
                                ->setName('Martynas Judickas')
                                ->setRow(30)
                                ->setExcelRow(31)
                                ->setSequenceNumber(27)
                        ),
                    (new Availability())
                        ->setDate('2024-11-05T08:00:00')
                        ->setDateTill('2024-11-05T20:00:00')
                        ->setAvailabilityType(Availability::UNAVAILABLE)
                        ->setEmployee(
                            (new Employee())
                                ->setName('Martynas Judickas')
                                ->setRow(30)
                                ->setExcelRow(31)
                                ->setSequenceNumber(27)
                        ),
                    (new Availability())
                        ->setDate('2024-11-03T20:00:00')
                        ->setDateTill('2024-11-04T08:00:00')
                        ->setAvailabilityType(Availability::UNAVAILABLE)
                        ->setEmployee(
                            (new Employee())
                                ->setName('Edgaras Baliūnas')
                                ->setRow(16)
                                ->setExcelRow(17)
                                ->setSequenceNumber(14)
                        ),
                    (new Availability())
                        ->setDate('2024-11-04T08:00:00')
                        ->setDateTill('2024-11-04T20:00:00')
                        ->setAvailabilityType(Availability::UNAVAILABLE)
                        ->setEmployee(
                            (new Employee())
                                ->setName('Edgaras Baliūnas')
                                ->setRow(16)
                                ->setExcelRow(17)
                                ->setSequenceNumber(14)
                        ),
                    (new Availability())
                        ->setDate('2024-11-04T20:00:00')
                        ->setDateTill('2024-11-05T08:00:00')
                        ->setAvailabilityType(Availability::AVAILABLE)
                        ->setEmployee(
                            (new Employee())
                                ->setName('Edgaras Baliūnas')
                                ->setRow(16)
                                ->setExcelRow(17)
                                ->setSequenceNumber(14)
                        ),
                    (new Availability())
                        ->setDate('2024-11-07T08:00:00')
                        ->setDateTill('2024-11-07T20:00:00')
                        ->setAvailabilityType(Availability::AVAILABLE)
                        ->setEmployee(
                            (new Employee())
                                ->setName('Edgaras Baliūnas')
                                ->setRow(16)
                                ->setExcelRow(17)
                                ->setSequenceNumber(14)
                        ),
                    (new Availability())
                        ->setDate('2024-11-07T20:00:00')
                        ->setDateTill('2024-11-08T08:00:00')
                        ->setAvailabilityType(Availability::AVAILABLE)
                        ->setEmployee(
                            (new Employee())
                                ->setName('Mantė Šmigelskaitė')
                                ->setRow(32)
                                ->setExcelRow(33)
                                ->setSequenceNumber(29)
                        ),
                    (new Availability())
                        ->setDate('2024-11-08T08:00:00')
                        ->setDateTill('2024-11-08T20:00:00')
                        ->setAvailabilityType(Availability::UNAVAILABLE)
                        ->setEmployee(
                            (new Employee())
                                ->setName('Mantė Šmigelskaitė')
                                ->setRow(32)
                                ->setExcelRow(33)
                                ->setSequenceNumber(29)
                        ),
                ]
            ],
        ];
    }
}