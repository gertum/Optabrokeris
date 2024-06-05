<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Hospital\ShiftsBuilder;
use App\Domain\Roster\Shift;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ShiftsBuilderTest extends TestCase
{
    /**
     * @dataProvider provideShiftsBuilderParameters
     * @param DateInterval[] $timeSlices
     */
    public function testBuildShifts(
        DateTimeImmutable $from,
        DateTimeImmutable $till,
        array $timeSlices,
        int $expectedShiftsAmount,
        int $shiftNumberToCheck,
        Shift $expectedShift
    ) {
        $shifts = ShiftsBuilder::buildShifts($from, $till, $timeSlices);
        $this->assertCount($expectedShiftsAmount, $shifts);
        $this->assertEquals($expectedShift, $shifts[$shiftNumberToCheck]);
    }

    public static function provideShiftsBuilderParameters(): array
    {
        return [
            'test 1' => [
                'from' => DateTimeImmutable::createFromFormat('Y-m-d H:i', '2024-06-01 00:00'),
                'till' => DateTimeImmutable::createFromFormat('Y-m-d H:i', '2024-06-02 00:00'),
                'timeSlices' => [
                    new DateInterval('PT8H'),
                    new DateInterval('PT12H'),
                    new DateInterval('PT4H'),
                ],
                'expectedShiftsAmount' => 3,
                'shiftNumberToCheck' => 0,
                'expectedShift' =>
                    (new Shift())
                        ->setId(1)
                        ->setStart("2024-06-01T00:00:00")
                        ->setEnd("2024-06-01T08:00:00")
            ],
            'test 2' => [
                'from' => DateTimeImmutable::createFromFormat('Y-m-d H:i', '2024-06-01 00:00'),
                'till' => DateTimeImmutable::createFromFormat('Y-m-d H:i', '2024-06-02 00:00'),
                'timeSlices' => [
                    new DateInterval('PT8H'),
                    new DateInterval('PT12H'),
                    new DateInterval('PT4H'),
                ],
                'expectedShiftsAmount' => 3,
                'shiftNumberToCheck' => 2,
                'expectedShift' =>
                    (new Shift())
                        ->setId(3)
                        ->setStart("2024-06-01T20:00:00")
                        ->setEnd("2024-06-02T00:00:00")
            ],
            'test 3' => [
                'from' => DateTimeImmutable::createFromFormat('Y-m-d H:i', '2024-06-01 00:00'),
                'till' => DateTimeImmutable::createFromFormat('Y-m-d H:i', '2024-06-03 00:00'),
                'timeSlices' => [
                    new DateInterval('PT8H'),
                    new DateInterval('PT12H'),
                    new DateInterval('PT4H'),
                ],
                'expectedShiftsAmount' => 6,
                'shiftNumberToCheck' => 4,
                'expectedShift' =>
                    (new Shift())
                        ->setId(5)
                        ->setStart("2024-06-02T08:00:00")
                        ->setEnd("2024-06-02T20:00:00")
            ],
        ];
    }
}