<?php


namespace Tests\Unit\Roster;


use App\Domain\Roster\Hospital\ShiftsBuilder;
use App\Domain\Roster\Shift;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ShiftsBuilderOfBoundsTest extends TestCase
{
    /**
     * @param float[] $shiftBounds
     * @dataProvider provideShiftsParameters
     */
    public function testBuildShifts(
        DateTimeImmutable $from,
        DateTimeImmutable $till,
        array $shiftBounds,
        int $expectedShiftsAmount,
        int $shiftNumberToCheck,
        Shift $expectedShift
    ) {
        $shifts = ShiftsBuilder::buildShiftsOfBounds($from, $till, $shiftBounds);

        $this->assertCount($expectedShiftsAmount, $shifts);

        $this->assertEquals($expectedShift, $shifts[$shiftNumberToCheck]);
    }

    public static function provideShiftsParameters(): array
    {
        return [
            'test1' => [
                'from' => DateTimeImmutable::createFromFormat('Y-m-d H:i', '2024-06-01 00:00'),
                'till' => DateTimeImmutable::createFromFormat('Y-m-d H:i', '2024-06-02 00:00'),
                'shiftBounds' => [0, 8, 20],
                'expectedShiftsAmount' => 3,
                'shiftNumberToCheck' => 0,
                'expectedShift' =>
                    (new Shift())
                        ->setId(1)
                        ->setStart("2024-06-01T00:00:00")
                        ->setEnd("2024-06-01T08:00:00")

            ]
        ];
    }

}