<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Hospital\ScheduleParser;
use App\Domain\Roster\Shift;
use PHPUnit\Framework\TestCase;

class ScheduleParserShiftsTest extends TestCase
{
    /**
     * @dataProvider provideDataForShiftParser
     */
    public function testParseSchedule (string $file, int $expectedShiftsCount,  int $shiftIndex, Shift $expectedShift) {
        $scheduleParser = new ScheduleParser();

        $schedule = $scheduleParser->parseScheduleXls($file, ScheduleParser::createHospitalTimeSlices());

        $this->assertCount($expectedShiftsCount, $schedule->shiftList);
        $shift = $schedule->shiftList[$shiftIndex];

        $this->assertEquals($expectedShift->start, $shift->start);
        $this->assertEquals($expectedShift->end, $shift->end);
    }

    public static function provideDataForShiftParser() : array {
        return [
            'test small' => [
                'file' => __DIR__ . '/data/small.xlsx',
                'expectedShiftsCount' => 18,
                'shiftIndex' => 17,
                'expectedShift'=> (new Shift())
                    ->setId(18)
                    ->setStart('2024-06-06T20:00:00')
                    ->setEnd('2024-06-07T00:00:00')
            ],
            'test birželis' => [
                'file' => __DIR__ . '/data/birželis.xlsx',
                'expectedShiftsCount' => 90,
                'shiftIndex' => 89,
                'expectedShift'=> (new Shift())
                    ->setId(90)
                    ->setStart('2024-06-30T20:00:00')
                    ->setEnd('2024-07-01T00:00:00')
            ],
        ];
    }
}