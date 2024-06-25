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
    public function testAssignment(string $from, string $till, Employee $employee, array $shifts,  int $expectedIndex, Shift $expectedShift) {
        $assignmentConsumer = new ShiftsAvailableAssignmentConsumer($shifts);
        $assignmentConsumer->setAssignment($from, $till, $employee);

         $this->assertEquals( $shifts[$expectedIndex]->start, $expectedShift->start );
         $this->assertEquals( $shifts[$expectedIndex]->end, $expectedShift->end );
         $this->assertEquals( $shifts[$expectedIndex]->employee->name, $expectedShift->employee->name);
    }

    public static function provideAssignmentData() : array {
        return [
            'test1' => [
                // from=1970-01-01 08:00:00, till=1970-01-01 20:00:00
                // impossible variation will need to assign day outside
//                'from' => '1970-01-01 08:00:00',
//                'till'=>'1970-01-01 20:00:00',
            // ---
                'from' => '2024-06-03T08:00:00',
                'till'=>'2024-06-03T20:00:00',
                'employee' => (new Employee())->setName('Marry'),
                'shifts' => [
                    (new Shift())
                        ->setStart('2024-06-03T08:00:00')
                        ->setEnd('2024-06-03T20:00:00')
                ],
                'expectedIndex' =>  0,
                'expectedShift' => (new Shift())
                    ->setStart('2024-06-03T08:00:00')
                    ->setEnd('2024-06-03T20:00:00')
                    ->setEmployee( (new Employee())->setName('Marry'))
            ],
            'test 2 handling next day' => [
                'from' => '2024-06-02T20:00:00',
                'till'=>'2024-06-03T00:00:00',
                'employee' => (new Employee())->setName('Marry'),
                'shifts' => [
                    (new Shift())
                        ->setStart('2024-06-02T20:00:00')
                        // this time means that the date is from the next day
                        ->setEnd('2024-06-03T00:00:00')
                ],
                'expectedIndex' =>  0,
                'expectedShift' => (new Shift())
                    ->setStart('2024-06-02T20:00:00')
                    ->setEnd('2024-06-03T00:00:00')
                    ->setEmployee( (new Employee())->setName('Marry'))
            ],
            'test 3 bigger shifts array' => [
                'from' => '2024-06-03T08:00:00',
                'till'=>'2024-06-03T20:00:00',
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
                'expectedIndex' =>  1,
                'expectedShift' => (new Shift())
                    ->setStart('2024-06-03T08:00:00')
                    ->setEnd('2024-06-03T20:00:00')
                    ->setEmployee( (new Employee())->setName('Marry'))
            ],
            'test 4 taking two shifts, check first' => [
                'from' => '2024-06-03T20:00:00',
                'till'=>'2024-06-03T08:00:00', // this should be the next  day time
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
                'expectedIndex' =>  2,
                'expectedShift' => (new Shift())
                    ->setStart('2024-06-03T20:00:00')
                    ->setEnd('2024-06-04T00:00:00')
                    ->setEmployee( (new Employee())->setName('Marry'))
            ],
            'test 4 taking two shifts, check second' => [
                'from' => '2024-06-03T20:00:00',
                'till'=>'2024-06-03T08:00:00', // this should be the next  day time
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
                'expectedIndex' =>  3,
                'expectedShift' => (new Shift())
                    ->setStart('2024-06-04T00:00:00')
                    ->setEnd('2024-06-04T08:00:00')
                    ->setEmployee( (new Employee())->setName('Marry'))
            ],
        ];
    }
}