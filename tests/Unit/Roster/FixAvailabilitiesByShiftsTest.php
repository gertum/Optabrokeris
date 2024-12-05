<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Employee;
use App\Domain\Roster\Schedule;
use App\Domain\Roster\Shift;
use PHPUnit\Framework\TestCase;

class FixAvailabilitiesByShiftsTest extends TestCase
{
    /**
     * @param Availability[] $expectedAvailabilities
     * @dataProvider provideSchedules
     */
    public function testCalculate(Schedule $schedule, array $expectedAvailabilities) {
        $availabilities = $schedule->recalculateAvailabilitiesByShifts();

        $this->assertEquals($expectedAvailabilities, $availabilities);
    }

    public static function provideSchedules() : array {
        return [
            'test0' => [
                'schedule' =>  new Schedule(),
                'expectedAvailabilities' => [],
            ],
            'test1 for 1 employee' => [
                'schedule' =>  (new Schedule())->setAvailabilityList([
                    (new Availability())
                        ->setDate('2024-11-01T00:00:00')
                        ->setDateTill('2024-11-02T00:00:00')
                        ->setAvailabilityType(Availability::AVAILABLE)
                        ->setEmployee((new Employee())->setName('Marry')),
                    (new Availability())
                        ->setDate('2024-11-02T00:00:00')
                        ->setDateTill('2024-11-03T00:00:00')
                        ->setAvailabilityType(Availability::UNAVAILABLE)
                        ->setEmployee((new Employee())->setName('Marry')),
                    (new Availability())
                        ->setDate('2024-11-03T00:00:00')
                        ->setDateTill('2024-11-04T00:00:00')
                        ->setAvailabilityType(Availability::DESIRED)
                        ->setEmployee((new Employee())->setName('Marry')),

                ])
                ->setShiftList([
                    (new Shift())->setStart('2024-11-01T08:00:00')->setEnd('2024-11-01T20:00:00'),
                    (new Shift())->setStart('2024-11-01T20:00:00')->setEnd('2024-11-02T08:00:00'),
                    (new Shift())->setStart('2024-11-02T08:00:00')->setEnd('2024-11-02T20:00:00'),
                    (new Shift())->setStart('2024-11-02T20:00:00')->setEnd('2024-11-03T08:00:00'),
                    (new Shift())->setStart('2024-11-03T08:00:00')->setEnd('2024-11-03T20:00:00'),
                    (new Shift())->setStart('2024-11-03T20:00:00')->setEnd('2024-11-04T08:00:00'),
                ])
                ->setEmployeeList([
                    (new Employee())->setName('Marry')
                ])
                ,
                'expectedAvailabilities' => [
                    (new Availability())
                        ->setDate('2024-11-01T08:00:00')
                        ->setDateTill('2024-11-01T20:00:00')
                        ->setAvailabilityType(Availability::AVAILABLE)
                        ->setEmployee((new Employee())->setName('Marry')->setSequenceNumber(1))
                        ->setId(1)
                    ,
                    (new Availability())
                        ->setDate('2024-11-01T20:00:00')
                        ->setDateTill('2024-11-02T08:00:00')
                        ->setAvailabilityType(Availability::AVAILABLE)
                        ->setEmployee((new Employee())->setName('Marry')->setSequenceNumber(1))
                        ->setId(2)
                    ,
                    (new Availability())
                        ->setDate('2024-11-02T08:00:00')
                        ->setDateTill('2024-11-02T20:00:00')
                        ->setAvailabilityType(Availability::UNAVAILABLE)
                        ->setEmployee((new Employee())->setName('Marry')->setSequenceNumber(1))
                        ->setId(3)
                    ,
                    (new Availability())
                        ->setDate('2024-11-02T20:00:00')
                        ->setDateTill('2024-11-03T08:00:00')
                        ->setAvailabilityType(Availability::UNAVAILABLE)
                        ->setEmployee((new Employee())->setName('Marry')->setSequenceNumber(1))
                        ->setId(4)
                    ,
                    (new Availability())
                        ->setDate('2024-11-03T08:00:00')
                        ->setDateTill('2024-11-03T20:00:00')
                        ->setAvailabilityType(Availability::DESIRED)
                        ->setEmployee((new Employee())->setName('Marry')->setSequenceNumber(1))
                        ->setId(5)
                    ,
                    (new Availability())
                        ->setDate('2024-11-03T20:00:00')
                        ->setDateTill('2024-11-04T08:00:00')
                        ->setAvailabilityType(Availability::DESIRED)
                        ->setEmployee((new Employee())->setName('Marry')->setSequenceNumber(1))
                        ->setId(6)
                    ,
                ],
            ],
            'test 2 for 2 employee' => [
                'schedule' =>  (new Schedule())->setAvailabilityList([
                    (new Availability())
                        ->setDate('2024-11-01T00:00:00')
                        ->setDateTill('2024-11-02T00:00:00')
                        ->setAvailabilityType(Availability::AVAILABLE)
                        ->setEmployee((new Employee())->setName('Marry'))
                        ->setId(1)
                    ,
                    (new Availability())
                        ->setDate('2024-11-02T00:00:00')
                        ->setDateTill('2024-11-03T00:00:00')
                        ->setAvailabilityType(Availability::UNAVAILABLE)
                        ->setEmployee((new Employee())->setName('Marry'))
                        ->setId(2)
                    ,
                    (new Availability())
                        ->setDate('2024-11-03T00:00:00')
                        ->setDateTill('2024-11-04T00:00:00')
                        ->setAvailabilityType(Availability::DESIRED)
                        ->setEmployee((new Employee())->setName('Marry'))
                        ->setId(3)
                    ,
                    (new Availability())
                        ->setDate('2024-11-01T00:00:00')
                        ->setDateTill('2024-11-02T00:00:00')
                        ->setAvailabilityType(Availability::DESIRED)
                        ->setEmployee((new Employee())->setName('Peter'))
                        ->setId(4)
                    ,
                    (new Availability())
                        ->setDate('2024-11-02T00:00:00')
                        ->setDateTill('2024-11-03T00:00:00')
                        ->setAvailabilityType(Availability::UNDESIRED)
                        ->setEmployee((new Employee())->setName('Peter'))
                        ->setId(5)
                    ,
                    (new Availability())
                        ->setDate('2024-11-03T00:00:00')
                        ->setDateTill('2024-11-04T00:00:00')
                        ->setAvailabilityType(Availability::UNAVAILABLE)
                        ->setEmployee((new Employee())->setName('Peter'))
                        ->setId(6)
                    ,

                ])
                ->setShiftList([
                    (new Shift())->setStart('2024-11-01T08:00:00')->setEnd('2024-11-01T20:00:00'),
                    (new Shift())->setStart('2024-11-01T20:00:00')->setEnd('2024-11-02T08:00:00'),
                    (new Shift())->setStart('2024-11-02T08:00:00')->setEnd('2024-11-02T20:00:00'),
                    (new Shift())->setStart('2024-11-02T20:00:00')->setEnd('2024-11-03T08:00:00'),
                    (new Shift())->setStart('2024-11-03T08:00:00')->setEnd('2024-11-03T20:00:00'),
                    (new Shift())->setStart('2024-11-03T20:00:00')->setEnd('2024-11-04T08:00:00'),
                ])
                ->setEmployeeList([
                    (new Employee())->setName('Marry'),
                    (new Employee())->setName('Peter'),
                ])
                ,
                'expectedAvailabilities' => [
                    (new Availability())
                        ->setDate('2024-11-01T08:00:00')
                        ->setDateTill('2024-11-01T20:00:00')
                        ->setAvailabilityType(Availability::AVAILABLE)
                        ->setEmployee((new Employee())->setName('Marry')->setSequenceNumber(1))
                        ->setId(1)
                    ,
                    (new Availability())
                        ->setDate('2024-11-01T20:00:00')
                        ->setDateTill('2024-11-02T08:00:00')
                        ->setAvailabilityType(Availability::AVAILABLE)
                        ->setEmployee((new Employee())->setName('Marry')->setSequenceNumber(1))
                        ->setId(2)
                    ,
                    (new Availability())
                        ->setDate('2024-11-02T08:00:00')
                        ->setDateTill('2024-11-02T20:00:00')
                        ->setAvailabilityType(Availability::UNAVAILABLE)
                        ->setEmployee((new Employee())->setName('Marry')->setSequenceNumber(1))
                        ->setId(3)
                    ,
                    (new Availability())
                        ->setDate('2024-11-02T20:00:00')
                        ->setDateTill('2024-11-03T08:00:00')
                        ->setAvailabilityType(Availability::UNAVAILABLE)
                        ->setEmployee((new Employee())->setName('Marry')->setSequenceNumber(1))
                        ->setId(4)
                    ,
                    (new Availability())
                        ->setDate('2024-11-03T08:00:00')
                        ->setDateTill('2024-11-03T20:00:00')
                        ->setAvailabilityType(Availability::DESIRED)
                        ->setEmployee((new Employee())->setName('Marry')->setSequenceNumber(1))
                        ->setId(5)
                    ,
                    (new Availability())
                        ->setDate('2024-11-03T20:00:00')
                        ->setDateTill('2024-11-04T08:00:00')
                        ->setAvailabilityType(Availability::DESIRED)
                        ->setEmployee((new Employee())->setName('Marry')->setSequenceNumber(1))
                        ->setId(6)
                    ,

                    (new Availability())
                        ->setDate('2024-11-01T08:00:00')
                        ->setDateTill('2024-11-01T20:00:00')
                        ->setAvailabilityType(Availability::DESIRED)
                        ->setEmployee((new Employee())->setName('Peter')->setSequenceNumber(2))
                        ->setId(7)
                    ,
                    (new Availability())
                        ->setDate('2024-11-01T20:00:00')
                        ->setDateTill('2024-11-02T08:00:00')
                        ->setAvailabilityType(Availability::DESIRED)
                        ->setEmployee((new Employee())->setName('Peter')->setSequenceNumber(2))
                        ->setId(8)
                    ,
                    (new Availability())
                        ->setDate('2024-11-02T08:00:00')
                        ->setDateTill('2024-11-02T20:00:00')
                        ->setAvailabilityType(Availability::UNDESIRED)
                        ->setEmployee((new Employee())->setName('Peter')->setSequenceNumber(2))
                        ->setId(9)
                    ,
                    (new Availability())
                        ->setDate('2024-11-02T20:00:00')
                        ->setDateTill('2024-11-03T08:00:00')
                        ->setAvailabilityType(Availability::UNDESIRED)
                        ->setEmployee((new Employee())->setName('Peter')->setSequenceNumber(2))
                        ->setId(10)
                    ,
                    (new Availability())
                        ->setDate('2024-11-03T08:00:00')
                        ->setDateTill('2024-11-03T20:00:00')
                        ->setAvailabilityType(Availability::UNAVAILABLE)
                        ->setEmployee((new Employee())->setName('Peter')->setSequenceNumber(2))
                        ->setId(11)
                    ,
                    (new Availability())
                        ->setDate('2024-11-03T20:00:00')
                        ->setDateTill('2024-11-04T08:00:00')
                        ->setAvailabilityType(Availability::UNAVAILABLE)
                        ->setEmployee((new Employee())->setName('Peter')->setSequenceNumber(2))
                        ->setId(12)
                    ,
                ],
            ],
        ];
    }
}