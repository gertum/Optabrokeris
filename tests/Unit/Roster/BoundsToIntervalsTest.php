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
            ],
            'test2' => [
                'bounds' => [
                    8,20
                ],
                'expectedIntervals' => [
                    new DateInterval('PT12H'),
                    new DateInterval('PT12H'),
                ]
            ],
            'test3' => [
                'bounds' => [
                    8.5,20
                ],
                'expectedIntervals' => [
                    new DateInterval('PT11H30M'),
                    new DateInterval('PT12H30M'),
                ]
            ],
        ];
    }

}