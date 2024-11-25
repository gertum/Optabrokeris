<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Employee;
use App\Domain\Roster\Hospital\Write\GroupedSchedule;
use App\Domain\Roster\Schedule;
use PHPUnit\Framework\TestCase;

class GroupedScheduleAvailabilitiesTest extends TestCase
{
    /**
     * @dataProvider provideSchedules
     * @param string[] $testEmployeeNames
     * @param string[] $testDatesFormatted
     * @param ?Availability[] $expectedAvailabilities
     */
    public function testGrouping(
        Schedule $schedule,
        array $testEmployeeNames,
        array $testDatesFormatted,
        array $expectedAvailabilities
    ) {
        $groupedSchedule = new GroupedSchedule();
        $groupedSchedule->importSchedule($schedule);

        for ($i = 0; $i < count($expectedAvailabilities); $i++) {
            $testEmployeeName = $testEmployeeNames[$i];
            $testDateFormatted = $testDatesFormatted[$i];
            $expectedAvailability = $expectedAvailabilities[$i];

            $availability = $groupedSchedule->findAvailability($testEmployeeName, $testDateFormatted);
            $this->assertEquals($expectedAvailability, $availability);
        }
    }

    public static function provideSchedules(): array
    {
        return [
            'test1' => [
                (new Schedule())->setEmployeeList(
                    [
                        (new Employee())->setName("Jonas Jonaitis")
                            ->setMaxWorkingHours(75),
                        (new Employee())->setName("Petras Petraitis")
                            ->setMaxWorkingHours(50),

                    ]
                )
                    ->setAvailabilityList(
                        [
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Jonas Jonaitis"))
                                ->setDate('2024-10-31T20:00:00')
                                ->setDateTill('2024-11-01T08:00:00')
                                ->setAvailabilityType(Availability::AVAILABLE),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Jonas Jonaitis"))
                                ->setDate('2024-11-01T08:00:00')
                                ->setDateTill('2024-11-01T20:00:00')
                                ->setAvailabilityType(Availability::UNAVAILABLE),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Jonas Jonaitis"))
                                ->setDate('2024-11-01T20:00:00')
                                ->setDateTill('2024-11-02T08:00:00')
                                ->setAvailabilityType(Availability::DESIRED),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Jonas Jonaitis"))
                                ->setDate('2024-11-02T08:00:00')
                                ->setDateTill('2024-11-02T20:00:00')
                                ->setAvailabilityType(Availability::DESIRED),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Jonas Jonaitis"))
                                ->setDate('2024-11-02T20:00:00')
                                ->setDateTill('2024-11-03T08:00:00')
                                ->setAvailabilityType(Availability::AVAILABLE),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Jonas Jonaitis"))
                                ->setDate('2024-11-02T08:00:00')
                                ->setDateTill('2024-11-03T20:00:00')
                                ->setAvailabilityType(Availability::AVAILABLE),

                            // petras petraitis
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Petras Petraitis"))
                                ->setDate('2024-10-30T20:00:00')
                                ->setDateTill('2024-11-01T08:00:00')
                                ->setAvailabilityType(Availability::AVAILABLE),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Petras Petraitis"))
                                ->setDate('2024-11-01T08:00:00')
                                ->setDateTill('2024-11-01T20:00:00')
                                ->setAvailabilityType(Availability::UNAVAILABLE),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Petras Petraitis"))
                                ->setDate('2024-11-01T20:00:00')
                                ->setDateTill('2024-11-02T08:00:00')
                                ->setAvailabilityType(Availability::UNDESIRED),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Petras Petraitis"))
                                ->setDate('2024-11-02T08:00:00')
                                ->setDateTill('2024-11-02T20:00:00')
                                ->setAvailabilityType(Availability::DESIRED),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Petras Petraitis"))
                                ->setDate('2024-11-02T20:00:00')
                                ->setDateTill('2024-11-03T08:00:00')
                                ->setAvailabilityType(Availability::UNAVAILABLE),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Petras Petraitis"))
                                ->setDate('2024-11-03T08:00:00')
                                ->setDateTill('2024-11-03T20:00:00')
                                ->setAvailabilityType(Availability::AVAILABLE),
                        ]
                    ),
                'testEmployeeNames' => [
                    'Jonas Jonaitis',
                    'Petras Petraitis',
                ],
                'testDatesFormatted' => [
                    '2024-11-01T08:00:00',
                    '2024-11-02T00:00:00',
                ],
                'expectedAvailabilities' => [
                    (new Availability())
                        ->setEmployee((new Employee())->setName("Jonas Jonaitis"))
                        ->setDate('2024-11-01T08:00:00')
                        ->setDateTill('2024-11-01T20:00:00')
                        ->setAvailabilityType(Availability::UNAVAILABLE),
                    (new Availability())
                        ->setEmployee((new Employee())->setName("Petras Petraitis"))
                        ->setDate('2024-11-01T20:00:00')
                        ->setDateTill('2024-11-02T08:00:00')
                        ->setAvailabilityType(Availability::UNDESIRED),
                ]
            ],

            // shuffled data
            'test1 shuffled' => [
                (new Schedule())->setEmployeeList(
                    [
                        (new Employee())->setName("Jonas Jonaitis")
                            ->setMaxWorkingHours(75),
                        (new Employee())->setName("Petras Petraitis")
                            ->setMaxWorkingHours(50),

                    ]
                )
                    ->setAvailabilityList(
                        [
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Jonas Jonaitis"))
                                ->setDate('2024-11-02T08:00:00')
                                ->setDateTill('2024-11-03T20:00:00')
                                ->setAvailabilityType(Availability::AVAILABLE),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Petras Petraitis"))
                                ->setDate('2024-11-02T08:00:00')
                                ->setDateTill('2024-11-02T20:00:00')
                                ->setAvailabilityType(Availability::DESIRED),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Jonas Jonaitis"))
                                ->setDate('2024-11-01T08:00:00')
                                ->setDateTill('2024-11-01T20:00:00')
                                ->setAvailabilityType(Availability::UNAVAILABLE),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Jonas Jonaitis"))
                                ->setDate('2024-11-01T20:00:00')
                                ->setDateTill('2024-11-02T08:00:00')
                                ->setAvailabilityType(Availability::DESIRED),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Jonas Jonaitis"))
                                ->setDate('2024-11-02T08:00:00')
                                ->setDateTill('2024-11-02T20:00:00')
                                ->setAvailabilityType(Availability::DESIRED),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Jonas Jonaitis"))
                                ->setDate('2024-11-02T20:00:00')
                                ->setDateTill('2024-11-03T08:00:00')
                                ->setAvailabilityType(Availability::AVAILABLE),

                            // petras petraitis
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Petras Petraitis"))
                                ->setDate('2024-11-03T08:00:00')
                                ->setDateTill('2024-11-03T20:00:00')
                                ->setAvailabilityType(Availability::AVAILABLE),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Petras Petraitis"))
                                ->setDate('2024-10-30T20:00:00')
                                ->setDateTill('2024-11-01T08:00:00')
                                ->setAvailabilityType(Availability::AVAILABLE),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Petras Petraitis"))
                                ->setDate('2024-11-01T08:00:00')
                                ->setDateTill('2024-11-01T20:00:00')
                                ->setAvailabilityType(Availability::UNAVAILABLE),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Petras Petraitis"))
                                ->setDate('2024-11-01T20:00:00')
                                ->setDateTill('2024-11-02T08:00:00')
                                ->setAvailabilityType(Availability::UNDESIRED),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Jonas Jonaitis"))
                                ->setDate('2024-10-30T20:00:00')
                                ->setDateTill('2024-11-01T08:00:00')
                                ->setAvailabilityType(Availability::AVAILABLE),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Petras Petraitis"))
                                ->setDate('2024-11-02T20:00:00')
                                ->setDateTill('2024-11-03T08:00:00')
                                ->setAvailabilityType(Availability::UNAVAILABLE),
                        ]
                    ),
                'testEmployeeNames' => [
                    'Jonas Jonaitis',
                    'Petras Petraitis',
                ],
                'testDatesFormatted' => [
                    '2024-11-01T08:00:00',
                    '2024-11-02T00:00:00',
                ],
                'expectedAvailabilities' => [
                    (new Availability())
                        ->setEmployee((new Employee())->setName("Jonas Jonaitis"))
                        ->setDate('2024-11-01T08:00:00')
                        ->setDateTill('2024-11-01T20:00:00')
                        ->setAvailabilityType(Availability::UNAVAILABLE),
                    (new Availability())
                        ->setEmployee((new Employee())->setName("Petras Petraitis"))
                        ->setDate('2024-11-01T20:00:00')
                        ->setDateTill('2024-11-02T08:00:00')
                        ->setAvailabilityType(Availability::UNDESIRED),
                ]
            ],

            // different availabilities array with non standard dates bounds
            'test non standard bounds' => [
                (new Schedule())->setEmployeeList(
                    [
                        (new Employee())->setName("Jonas Jonaitis")
                            ->setMaxWorkingHours(75),
                        (new Employee())->setName("Petras Petraitis")
                            ->setMaxWorkingHours(50),

                    ]
                )
                    ->setAvailabilityList(
                        [
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Jonas Jonaitis"))
                                ->setDate('2024-11-01T00:00:00')
                                ->setDateTill('2024-11-02T00:00:00')
                                ->setAvailabilityType(Availability::AVAILABLE),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Jonas Jonaitis"))
                                ->setDate('2024-11-02T00:00:00')
                                ->setDateTill('2024-11-03T00:00:00')
                                ->setAvailabilityType(Availability::UNAVAILABLE),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Jonas Jonaitis"))
                                ->setDate('2024-11-03T00:00:00')
                                ->setDateTill('2024-11-04T00:00:00')
                                ->setAvailabilityType(Availability::DESIRED),
                        ]
                    ),
                'testEmployeeNames' => [
                    'Jonas Jonaitis',
                ],
                'testDatesFormatted' => [
                    '2024-11-01T00:00:00',
                ],
                'expectedAvailabilities' => [
                    (new Availability())
                        ->setEmployee((new Employee())->setName("Jonas Jonaitis"))
                        ->setDate('2024-11-01T00:00:00')
                        ->setDateTill('2024-11-02T00:00:00')
                        ->setAvailabilityType(Availability::AVAILABLE),
                ]
            ],

            // empty availabilities array
            'test empty' => [
                (new Schedule())->setEmployeeList(
                    [
                        (new Employee())->setName("Jonas Jonaitis")
                            ->setMaxWorkingHours(75),
                        (new Employee())->setName("Petras Petraitis")
                            ->setMaxWorkingHours(50),

                    ]
                )
                    ->setAvailabilityList(
                        [
                        ]
                    ),
                'testEmployeeNames' => [
                    'Jonas Jonaitis',
                ],
                'testDatesFormatted' => [
                    '2024-11-01T00:00:00',
                ],
                'expectedAvailabilities' => [
                    null,
                ]
            ],


            // search date out of range above and below
            'test out of bounds' => [
                (new Schedule())->setEmployeeList(
                    [
                        (new Employee())->setName("Jonas Jonaitis")
                            ->setMaxWorkingHours(75),
                        (new Employee())->setName("Petras Petraitis")
                            ->setMaxWorkingHours(50),

                    ]
                )
                    ->setAvailabilityList(
                        [
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Jonas Jonaitis"))
                                ->setDate('2024-11-01T00:00:00')
                                ->setDateTill('2024-11-02T00:00:00')
                                ->setAvailabilityType(Availability::AVAILABLE),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Jonas Jonaitis"))
                                ->setDate('2024-11-02T00:00:00')
                                ->setDateTill('2024-11-03T00:00:00')
                                ->setAvailabilityType(Availability::UNAVAILABLE),
                            (new Availability())
                                ->setEmployee((new Employee())->setName("Jonas Jonaitis"))
                                ->setDate('2024-11-03T00:00:00')
                                ->setDateTill('2024-11-04T00:00:00')
                                ->setAvailabilityType(Availability::DESIRED),
                        ]
                    ),
                'testEmployeeNames' => [
                    'Jonas Jonaitis',
                    'Jonas Jonaitis',
                    'Jonas Jonaitis',
                ],
                'testDatesFormatted' => [
                    '2024-12-10T00:00:00',
                    '2024-10-29T00:00:00',
//                    // very near
                    '2024-10-31T13:00:00'
                ],
                'expectedAvailabilities' => [
                    (new Availability())
                        ->setEmployee((new Employee())->setName("Jonas Jonaitis"))
                        ->setDate('2024-11-03T00:00:00')
                        ->setDateTill('2024-11-04T00:00:00')
                        ->setAvailabilityType(Availability::DESIRED),
                    null,
                    null,
                ]
            ],

            // non necessary? :
            // gaped availabilities date intervals may be later if needed

        ];
    }

}