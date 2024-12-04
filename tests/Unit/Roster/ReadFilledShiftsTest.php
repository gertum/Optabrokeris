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

        if ( $expectedShift->employee == null ) {
            $this->assertNull($shift->employee);
        }
        else {
            $this->assertNotNull($shift->employee);
            $this->assertEquals($expectedShift->employee->name, $shift->employee->name);
        }
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
            'test1_3' => [
                'xslxFile'  => __DIR__.'/data/small_partly_filled.xlsx',
                'expectedShift' => (new Shift())
                ->setStart('2024-06-06T00:00:00')
                ->setEnd('2024-06-06T08:00:00')
                ->setEmployee(
                    (new Employee())
                        ->setName("Renata Juknevičienė 29/12")
                )
            ],
            'test 2' => [
                'xslxFile'  => __DIR__.'/data/smallfilled.xlsx',
                'expectedShift' => (new Shift())
                ->setStart('2024-06-01T00:00:00')
                ->setEnd('2024-06-01T08:00:00')
                ->setEmployee(
                    (new Employee())
                        ->setName("Aleksandras Briedis 24/12")
                )
            ],
            'test 8-24 -> 8-20' => [
                'xslxFile'  => __DIR__.'/data/small_partly_filled_complex.xlsx',
                'expectedShift' => (new Shift())
                ->setStart('2024-06-05T08:00:00')
                ->setEnd('2024-06-05T20:00:00')
                ->setEmployee(
                    (new Employee())
                        ->setName("Aleksandras Briedis 24/12")
                )
            ],
            'test 8-24 -> 20-24' => [
                'xslxFile'  => __DIR__.'/data/small_partly_filled_complex.xlsx',
                'expectedShift' => (new Shift())
                ->setStart('2024-06-05T20:00:00')
                ->setEnd('2024-06-06T00:00:00')
                ->setEmployee(
                    (new Employee())
                        ->setName("Aleksandras Briedis 24/12")
                )
            ],
            'test 8-24 -> 8-20 (0)' => [
                'xslxFile'  => __DIR__.'/data/small_partly_filled_complex.xlsx',
                'expectedShift' => (new Shift())
                ->setStart('2024-06-04T08:00:00')
                ->setEnd('2024-06-04T20:00:00')
                ->setEmployee(
                    (new Employee())
                        ->setName("Renata Juknevičienė 29/12")
                )
            ],
            'test 8-24 -> 20-0 (0)' => [
                'xslxFile'  => __DIR__.'/data/small_partly_filled_complex.xlsx',
                'expectedShift' => (new Shift())
                ->setStart('2024-06-04T20:00:00')
                ->setEnd('2024-06-05T00:00:00')
                ->setEmployee(
                    (new Employee())
                        ->setName("Renata Juknevičienė 29/12")
                )
            ],

            // test with interval 08:00 - 24:00 ( 08:00 - 00:00 )

            // parse already solved xlsx
            'test parse solved' => [
                'xslxFile'  => __DIR__.'/data/solved.xlsx',
                'expectedShift' => (new Shift())
                    ->setStart('2024-11-06T08:00:00')
                    ->setEnd('2024-11-06T20:00:00')
                    ->setEmployee(
                        (new Employee())
                            ->setName("Vilma Grakauskienė")
                    )
            ],
        ];
    }
}