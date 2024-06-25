<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Employee;
use App\Domain\Roster\Hospital\ScheduleParser;
use App\Domain\Roster\Shift;
use PHPUnit\Framework\TestCase;

class ReadFilledShiftsTest extends TestCase
{
    /**
     * @dataProvider provideShiftsData
     */
    public function testReadFilled(string $xslxFile, Shift $expectedShift) {
        $scheduleParser = new ScheduleParser();

        $schedule = $scheduleParser->parseScheduleXls($xslxFile, ScheduleParser::createHospitalTimeSlices());

        $shift = $schedule->findShiftByStartDate( $expectedShift->start);

        $this->assertEquals($expectedShift->start, $shift->start );
        $this->assertEquals($expectedShift->end, $shift->end );

        $this->assertEquals( $expectedShift->employee->name, $shift->employee->name);
    }

    public static function provideShiftsData() : array {
        return [
            'test1_1' => [
                'xslxFile'  => __DIR__.'/data/small_partly_filled.xlsx',
                'expectedShift' => (new Shift())
                ->setStart('2024-06-03T08:00:00')
                ->setEnd('2024-06-03T20:00:00')
                ->setEmployee(
                    (new Employee())
                        ->setName("Renata Juknevičienė 29/12")
                )
            ],
            'test1_2' => [
                'xslxFile'  => __DIR__.'/data/small_partly_filled.xlsx',
                'expectedShift' => (new Shift())
                ->setStart('2024-06-02T20:00:00')
                ->setEnd('2024-06-03T00:00:00')
                ->setEmployee(
                    (new Employee())
                        ->setName("Aleksandras Briedis 24/12")
                )
            ],
        ];
    }
}