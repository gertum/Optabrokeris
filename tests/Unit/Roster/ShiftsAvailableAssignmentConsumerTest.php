<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Employee;
use App\Domain\Roster\Hospital\ShiftsAvailableAssignmentConsumer;
use App\Domain\Roster\Shift;
use PHPUnit\Framework\TestCase;

class ShiftsAvailableAssignmentConsumerTest extends TestCase
{

    /**
     * @param Shift[] $shifts
     * @dataProvider provideAssignmentData
     */
    public function testAssignment(
        string $from,
        string $till,
        Employee $employee,
        array $shifts,
        int $expectedIndex,
        Shift $expectedShift
    ) {
        $assignmentConsumer = new ShiftsAvailableAssignmentConsumer($shifts);
        $assignmentConsumer->setAssignment($from, $till, $employee);

        if ($expectedIndex < 0) {
            $this->assertTrue(true);
            return;
        }

        $this->assertEquals($shifts[$expectedIndex]->start, $expectedShift->start);
        $this->assertEquals($shifts[$expectedIndex]->end, $expectedShift->end);

        if ( $expectedShift->employee == null ) {
            $this->assertNull($shifts[$expectedIndex]->employee);
        }
        else {
            $this->assertNotNull($shifts[$expectedIndex]->employee);
            $this->assertEquals($shifts[$expectedIndex]->employee->name, $expectedShift->employee->name);
        }
    }

    public static function provideAssignmentData(): array
    {
        return [
            'test1' => [
                'from' => '2024-06-03T08:00:00',
                'till' => '2024-06-03T20:00:00',
                'employee' => (new Employee())->setName('Marry'),
                'shifts' => [
                    (new Shift())
                        ->setStart('2024-06-03T08:00:00')
                        ->setEnd('2024-06-03T20:00:00')
                ],
                'expectedIndex' => 0,
                'expectedShift' => (new Shift())
                    ->setStart('2024-06-03T08:00:00')
                    ->setEnd('2024-06-03T20:00:00')
                    ->setEmployee((new Employee())->setName('Marry'))
            ],
            'test 2 handling next day' => [
                'from' => '2024-06-02T20:00:00',
                'till' => '2024-06-03T00:00:00',
                'employee' => (new Employee())->setName('Marry'),
                'shifts' => [
                    (new Shift())
                        ->setStart('2024-06-02T20:00:00')
                        // this time means that the date is from the next day
                        ->setEnd('2024-06-03T00:00:00')
                ],
                'expectedIndex' => 0,
                'expectedShift' => (new Shift())
                    ->setStart('2024-06-02T20:00:00')
                    ->setEnd('2024-06-03T00:00:00')
                    ->setEmployee((new Employee())->setName('Marry'))
            ],
            'test 3 bigger shifts array' => [
                'from' => '2024-06-03T08:00:00',
                'till' => '2024-06-03T20:00:00',
                'employee' => (new Employee())->setName('Marry'),
                'shifts' => [
                    (new Shift())
                        ->setStart('2024-06-03T00:00:00')
                        ->setEnd('2024-06-03T08:00:00'),
                    (new Shift())
                        ->setStart('2024-06-03T08:00:00')
                        ->setEnd('2024-06-03T20:00:00'),
                    (new Shift())
                        ->setStart('2024-06-03T20:00:00')
                        ->setEnd('2024-06-04T00:00:00'),
                    (new Shift())
                        ->setStart('2024-06-04T00:00:00')
                        ->setEnd('2024-06-04T08:00:00'),
                ],
                'expectedIndex' => 1,
                'expectedShift' => (new Shift())
                    ->setStart('2024-06-03T08:00:00')
                    ->setEnd('2024-06-03T20:00:00')
                    ->setEmployee((new Employee())->setName('Marry'))
            ],
            'test 4 taking two shifts, check first' => [
                'from' => '2024-06-03T20:00:00',
                'till' => '2024-06-03T08:00:00', // this should be the next  day time
                'employee' => (new Employee())->setName('Marry'),
                'shifts' => [
                    (new Shift())
                        ->setStart('2024-06-03T00:00:00')
                        ->setEnd('2024-06-03T08:00:00'),
                    (new Shift())
                        ->setStart('2024-06-03T08:00:00')
                        ->setEnd('2024-06-03T20:00:00'),
                    (new Shift())
                        ->setStart('2024-06-03T20:00:00')
                        ->setEnd('2024-06-04T00:00:00'),
                    (new Shift())
                        ->setStart('2024-06-04T00:00:00')
                        ->setEnd('2024-06-04T08:00:00'),
                    (new Shift())
                        ->setStart('2024-06-04T08:00:00')
                        ->setEnd('2024-06-04T20:00:00'),
                ],
                'expectedIndex' => 2,
                'expectedShift' => (new Shift())
                    ->setStart('2024-06-03T20:00:00')
                    ->setEnd('2024-06-04T00:00:00')
                    ->setEmployee((new Employee())->setName('Marry'))
            ],
            'test 4 taking two shifts, check second' => [
                'from' => '2024-06-03T20:00:00',
                'till' => '2024-06-03T08:00:00', // this should be the next  day time
                'employee' => (new Employee())->setName('Marry'),
                'shifts' => [
                    (new Shift())
                        ->setStart('2024-06-03T00:00:00')
                        ->setEnd('2024-06-03T08:00:00'),
                    (new Shift())
                        ->setStart('2024-06-03T08:00:00')
                        ->setEnd('2024-06-03T20:00:00'),
                    (new Shift())
                        ->setStart('2024-06-03T20:00:00')
                        ->setEnd('2024-06-04T00:00:00'),
                    (new Shift())
                        ->setStart('2024-06-04T00:00:00')
                        ->setEnd('2024-06-04T08:00:00'),
                    (new Shift())
                        ->setStart('2024-06-04T08:00:00')
                        ->setEnd('2024-06-04T20:00:00'),
                ],
                'expectedIndex' => 3,
                'expectedShift' => (new Shift())
                    ->setStart('2024-06-04T00:00:00')
                    ->setEnd('2024-06-04T08:00:00')
                    ->setEmployee((new Employee())->setName('Marry'))
            ],

            'test 5 when not found' => [
                'from' => '2024-06-03T20:00:00',
                'till' => '2024-06-03T08:00:00', // this should be the next  day time
                'employee' => (new Employee())->setName('Marry'),
                'shifts' => [
                    (new Shift())
                        ->setStart('2024-06-01T00:00:00')
                        ->setEnd('2024-06-01T08:00:00'),
                ],
                'expectedIndex' => 0,
                'expectedShift' => (new Shift())
                    ->setStart('2024-06-01T00:00:00')
                    ->setEnd('2024-06-01T08:00:00')
                    ->setEmployee(null)
            ],
            'test 6 when no shifts' => [
                'from' => '2024-06-03T20:00:00',
                'till' => '2024-06-03T08:00:00', // this should be the next  day time
                'employee' => (new Employee())->setName('Marry'),
                'shifts' => [
                ],
                'expectedIndex' => -1,
                'expectedShift' => (new Shift())
                    ->setStart('2024-06-01T00:00:00')
                    ->setEnd('2024-06-01T08:00:00')
                    ->setEmployee(null)
            ],
            'test 7 differs by a seconds part' => [
                'from' => '2024-11-06T08:00:09',
                'till' => '2024-11-06T20:00:09', // this should be the next  day time
                'employee' => (new Employee())->setName('Marry'),
                'shifts' => [
                    (new Shift())
                        ->setStart('2024-11-05T20:00:00')
                        ->setEnd('2024-11-06T00:00:00'),
                    (new Shift())
                        ->setStart('2024-11-06T00:00:00')
                        ->setEnd('2024-11-06T08:00:00'),
                    (new Shift())
                        ->setStart('2024-11-06T08:00:00')
                        ->setEnd('2024-11-06T20:00:00'),
                    (new Shift())
                        ->setStart('2024-11-06T20:00:00')
                        ->setEnd('2024-11-07T00:00:00'),
                ],
                'expectedIndex' => 2,
                'expectedShift' => (new Shift())
                    ->setStart('2024-11-06T08:00:00')
                    ->setEnd('2024-11-06T20:00:00')
                    ->setEmployee((new Employee())->setName('Marry'))
            ],
            'test 7 differs by a seconds part, if not too much' => [
                'from' => '2024-11-06T08:00:09',
                'till' => '2024-11-06T20:00:09', // this should be the next  day time
                'employee' => (new Employee())->setName('Marry'),
                'shifts' => [
                    (new Shift())
                        ->setStart('2024-11-05T20:00:00')
                        ->setEnd('2024-11-06T00:00:00'),
                    (new Shift())
                        ->setStart('2024-11-06T00:00:00')
                        ->setEnd('2024-11-06T08:00:00'),
                    (new Shift())
                        ->setStart('2024-11-06T08:00:00')
                        ->setEnd('2024-11-06T20:00:00'),
                    (new Shift())
                        ->setStart('2024-11-06T20:00:00')
                        ->setEnd('2024-11-07T00:00:00'),
                ],
                'expectedIndex' => 3,
                'expectedShift' => (new Shift())
                    ->setStart('2024-11-06T20:00:00')
                    ->setEnd('2024-11-07T00:00:00')
                    ->setEmployee(null)
            ],

            // This case is not working by the requirements
            // test with interval 08:00 - 24:00 ( 08:00 - 00:00 )
//            'test 8-24' => [
//                'from' => '2024-06-05T08:00:00',
//                'till' => '2024-06-03T20:00:00', // this should be the next  day time
//                'employee' => (new Employee())->setName('Aleksandras Briedis 24/12'),
//                'shifts' => [
//                ],
//                'expectedIndex' => 14,
//                'expectedShift' => (new Shift())
//                    ->setStart('2024-06-05T08:00:00')
//                    ->setEnd('2024-06-05T20:00:00')
//                    ->setEmployee((new Employee())->setName('Aleksandras Briedis 24/12'))
//            ],
        ];
    }
}