<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Schedule;
use App\Domain\Roster\Shift;
use PHPUnit\Framework\TestCase;

class FixByShiftsBoundsTest extends TestCase
{
    /**
     * @param float[] $bounds
     * @dataProvider provideShiftsAndBounds
     */
    public function testFix(Schedule $originalSchedule, array $bounds, array $expectedShifts)
    {
        $shifts = $originalSchedule->recalculateShiftsByBounds($bounds);

        $this->assertEquals($expectedShifts, $shifts);
    }

    public static function provideShiftsAndBounds(): array
    {
        return [
            'test0' => [
                'originalSchedule' => new Schedule(),
                'bounds' => [8,12],
                'expectedShifts' => [],
            ],
            'test1' => [
                'originalSchedule' =>
                    (new Schedule())->setShiftList([
                        (new Shift())->setStart('2024-11-01T20:00:00')->setEnd('2024-11-02T00:00:00'),
                        (new Shift())->setStart('2024-11-02T00:00:00')->setEnd('2024-11-02T08:00:00'),
                        (new Shift())->setStart('2024-11-02T08:00:00')->setEnd('2024-11-02T20:00:00'),
                    ]),
                'bounds' => [8, 12],
                'expectedShifts' =>
                    [
                        (new Shift())->setStart('2024-11-01T20:00:00')->setEnd('2024-11-02T08:00:00'),
                        (new Shift())->setStart('2024-11-02T08:00:00')->setEnd('2024-11-02T20:00:00'),
                    ],

            ],
            'test2 not exact dates' => [
                'originalSchedule' =>
                    (new Schedule())->setShiftList([
                        (new Shift())->setStart('2024-11-01T20:00:04')->setEnd('2024-11-02T00:00:01'),
                        (new Shift())->setStart('2024-11-02T00:00:09')->setEnd('2024-11-02T08:00:03'),
                        (new Shift())->setStart('2024-11-02T08:00:07')->setEnd('2024-11-02T20:00:05'),
                    ]),
                'bounds' => [8, 12],
                'expectedShifts' =>
                    [
                        (new Shift())->setStart('2024-11-01T20:00:04')->setEnd('2024-11-02T08:00:03'),
                        (new Shift())->setStart('2024-11-02T08:00:07')->setEnd('2024-11-02T20:00:05'),
                    ],

            ],

//            'test3 absolutely different dates' => [
//                'originalSchedule' =>
//                    (new Schedule())->setShiftList([
//                        (new Shift())->setStart('2024-11-01T00:00:00')->setEnd('2024-11-01T12:00:00'),
//                        (new Shift())->setStart('2024-11-01T12:00:09')->setEnd('2024-11-02T00:00:00'),
//                        (new Shift())->setStart('2024-11-02T00:00:00')->setEnd('2024-11-02T12:00:00'),
//                        (new Shift())->setStart('2024-11-02T12:00:00')->setEnd('2024-11-03T00:00:00'),
//                    ]),
//                'bounds' => [8, 20],
//                'expectedShifts' =>
//                    [
//                        (new Shift())->setStart('2024-11-01T08:00:00')->setEnd('2024-11-01T20:00:00'),
//                        (new Shift())->setStart('2024-11-01T20:00:00')->setEnd('2024-11-02T08:00:00'),
//                        (new Shift())->setStart('2024-11-02T08:00:00')->setEnd('2024-11-02T20:00:00'),
//                    ],
//
//            ],

        ];
    }
}