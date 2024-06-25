<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Employee;
use App\Domain\Roster\Hospital\ShiftsAvailableAssgnmentConsumer;
use App\Domain\Roster\Shift;
use PHPUnit\Framework\TestCase;

class ShiftsAvailableAssgnmentConsumerTest extends TestCase
{

    /**
     * @param Shift[] $shifts
     */
    public function testAssignment(string $from, string $till, array $shifts, int $expectedIndex, Shift $expectedShift) {
        $assignmentConsumer = new ShiftsAvailableAssgnmentConsumer($shifts);

        $employee = new Employee();

        $assignmentConsumer->setAssignment($from, $till, $employee);

         $this->assertEquals( $shifts[$expectedIndex]->start, $expectedShift->start );
         $this->assertEquals( $shifts[$expectedIndex]->end, $expectedShift->end );
         $this->assertEquals( $shifts[$expectedIndex]->employee, $expectedShift->employee);
    }

    public static function provideAssignmentData() : array {
        return [
            // TODO
//            'test1' => [
//
//            ],
        ];
    }
}