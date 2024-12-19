<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Employee;
use App\Domain\Roster\Schedule;
use Carbon\Carbon;
use Carbon\Exceptions\Exception;
use PHPUnit\Framework\TestCase;
use Spatie\DataTransferObject\DataTransferObject;

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
        $availability = $schedule->findAvailability($employeeName, $searchDate);
        $this->assertEquals($expectedAvailability->date, $availability->date);
        $this->assertEquals($expectedAvailability->employee->name, $availability->employee->name);
    }

    public static function provideSchedules(): array
    {
        return [
            'test1' => [
                'schedule' => (new Schedule())
                    ->setEmployeeList(
                        [
                            (new Employee())->setName('Jonas')
                        ]
                    )
                    ->setAvailabilityList(
                        [
                            (new Availability())
                                ->setDate('2024-11-01')
                                ->setEmployee((new Employee())->setName('Jonas'))
                        ]
                    )
                ,
                'employeeName' => 'Jonas',
                'searchDate' => '2024-11-01',
                'expectedAvailability' => (new Availability())
                    ->setDate('2024-11-01')
                    ->setEmployee((new Employee())->setName('Jonas')),
            ],
            'test 2 carbon' => [
                'schedule' => (new Schedule())
                    ->setEmployeeList(
                        [
                            (new Employee())->setName('Jonas')
                        ]
                    )
                    ->setAvailabilityList(
                        [
                            (new Availability())
                                ->setDate(Carbon::parse( '2024-11-01'))
                                ->setEmployee((new Employee())->setName('Jonas'))
                        ]
                    )
                ,
                'employeeName' => 'Jonas',
                'searchDate' => '2024-11-01',
                'expectedAvailability' => (new Availability())
                    ->setDate('2024-11-01')
                    ->setEmployee((new Employee())->setName('Jonas')),
            ]
        ];
    }
}