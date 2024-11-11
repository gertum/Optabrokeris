<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Schedule;
use App\Domain\Roster\Shift;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class DetectMonthDateTest extends TestCase
{
    /**
     * @dataProvider provideSchedules
     */
    public function testDetect(Schedule $schedule, ?Carbon $expectedDate)
    {
        $date = $schedule->detectMonthDate();

        $this->assertEquals($expectedDate, $date);
    }

    public static function provideSchedules(): array
    {
        return [
            // ----
            'test1' => [
                'schedule' => (new Schedule())->setShiftList(
                    [
                        (new Shift())
                            ->setStart('2024-10-30T20:00:00')
                            ->setEnd('2024-11-01T08:00:00'),
                        (new Shift())
                            ->setStart('2024-11-01T08:00:00')
                            ->setEnd('2024-11-01T20:00:00'),
                        (new Shift())
                            ->setStart('2024-11-01T20:00:00')
                            ->setEnd('2024-11-02T08:00:00'),
                    ]
                ),
                'expectedDate' => Carbon::create(2024, 11),
            ],
            // ----
            'test empty' => [
                'schedule' => (new Schedule())->setShiftList([]),
                'expectedDate' => null,
            ],
        ];
    }
}