<?php


namespace Tests\Unit\Roster;


use App\Domain\Roster\Hospital\ShiftsBuilder;
use PHPUnit\Framework\TestCase;
use DateInterval;

class BoundsToIntervalsTest extends TestCase
{
    /**
     * @param float[] $bounds
     * @param DateInterval[] $expectedIntervals
     * @dataProvider provideBounds
     */
    public function testTransform(array $bounds, array $expectedIntervals) {
        $intervals = ShiftsBuilder::transformBoundsToTimeSlices($bounds);

        $this->assertEquals($expectedIntervals, $intervals);
    }

    public static function provideBounds() : array {
        return [
            'test1' => [
                'bounds' => [
                    0,8,20
                ],
                'expectedIntervals' => [
                    new DateInterval('PT8H'),
                    new DateInterval('PT12H'),
                    new DateInterval('PT4H'),
                ]
            ]
        ];
    }

}