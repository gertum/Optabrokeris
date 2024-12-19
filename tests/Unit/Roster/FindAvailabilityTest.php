<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Employee;
use App\Domain\Roster\Schedule;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class FindAvailabilityTest extends TestCase
{
    /**
     * @dataProvider provideSchedules
     */
    public function testFindAvailability(
        Schedule $schedule,
        string $employeeName,
        string $searchDate,
        Availability $expectedAvailability
    ) {
        $schedule->referenceEmployeesToAvailabilities();
        $schedule->assignEmployeesSequenceNumbers();
        $schedule->sortAvailabilities();
        $availability = $schedule->findAvailability($employeeName, $searchDate, true);
        $this->assertEquals($expectedAvailability->getCarbonDate(), $availability->getCarbonDate());
        $this->assertEquals($expectedAvailability->employee->name, $availability->employee->name);
    }

    public static function provideSchedules(): array
    {
        return [
//            'test1' => [
//                'schedule' => (new Schedule())
//                    ->setEmployeeList(
//                        [
//                            (new Employee())->setName('Jonas')
//                        ]
//                    )
//                    ->setAvailabilityList(
//                        [
//                            (new Availability())
//                                ->setDate('2024-11-01')
//                                ->setEmployee((new Employee())->setName('Jonas'))
//                        ]
//                    )
//                ,
//                'employeeName' => 'Jonas',
//                'searchDate' => '2024-11-01',
//                'expectedAvailability' => (new Availability())
//                    ->setDate('2024-11-01')
//                    ->setEmployee((new Employee())->setName('Jonas')),
//            ],
//            'test 2 carbon' => [
//                'schedule' => (new Schedule())
//                    ->setEmployeeList(
//                        [
//                            (new Employee())->setName('Jonas')
//                        ]
//                    )
//                    ->setAvailabilityList(
//                        [
//                            (new Availability())
//                                ->setDate(Carbon::parse( '2024-11-01'))
//                                ->setEmployee((new Employee())->setName('Jonas'))
//                        ]
//                    )
//                ,
//                'employeeName' => 'Jonas',
//                'searchDate' => '2024-11-01',
//                'expectedAvailability' => (new Availability())
//                    ->setDate('2024-11-01')
//                    ->setEmployee((new Employee())->setName('Jonas')),
//            ],
            'test 3 carbon' => [
                'schedule' => (new Schedule())
                    ->setEmployeeList(
                        [
                            (new Employee())->setName('Jonas')
                        ]
                    )
                    ->setAvailabilityList(
                        [
                            (new Availability())
                                ->setDate(Carbon::parse( '2024-11-01T00:00:00'))
                                ->setDateTill(Carbon::parse( '2024-11-02T00:00:00'))
                                ->setEmployee((new Employee())->setName('Jonas')),
                            (new Availability())
                                ->setDate(Carbon::parse( '2024-11-02T00:00:00'))
                                ->setDateTill(Carbon::parse( '2024-11-03T00:00:00'))
                                ->setEmployee((new Employee())->setName('Jonas')),
                            (new Availability())
                                ->setDate(Carbon::parse( '2024-11-03T00:00:00'))
                                ->setDateTill(Carbon::parse( '2024-11-04T00:00:00'))
                                ->setEmployee((new Employee())->setName('Jonas')),
                        ]
                    )
                ,
                'employeeName' => 'Jonas',
                'searchDate' => '2024-11-01T08:00:00',
                'expectedAvailability' => (new Availability())
                    ->setDate('2024-11-01')
                    ->setEmployee((new Employee())->setName('Jonas')),
            ]
        ];
    }
}