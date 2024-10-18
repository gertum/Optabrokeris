<?php

namespace Tests\Unit\Roster\Write;

use App\Domain\Roster\Employee;
use App\Domain\Roster\Hospital\Write\DayOccupation;
use App\Domain\Roster\Hospital\Write\ShiftsListTransformer;
use App\Domain\Roster\Shift;
use Tests\TestCase;

class ShiftsTransformerTest extends TestCase
{
    /**
     * @param Shift[] $shifts
     * @param DayOccupation[] $expectedOccupations
     * @dataProvider provideShiftsData
     */
    public function testTransform(array $shifts, array $expectedOccupations)
    {
        $occupations = ShiftsListTransformer::transform($shifts);

        $this->assertEquals($expectedOccupations, $occupations);
    }

    public static function provideShiftsData(): array
    {
        return [
            'test1' => [
                'shifts' => [
                    (new Shift())
                        ->setStart('2024-02-01T00:00:00')
                        ->setEnd('2024-02-01T08:00:00')
                        ->setEmployee((new Employee())->setName('Peter')),
                ],
                'expectedOccupations' => [
                    (new DayOccupation())
                        ->setDay(1)
                        ->setStartHour(0.0)
                        ->setEndHour(8.0)
                        ->setEmployee((new Employee())->setName('Peter')),
                ],
            ],
            'test2' => [
                'shifts' => [
                    (new Shift())
                        ->setStart('2024-02-01T00:00:00')
                        ->setEnd('2024-02-01T08:00:00')
                        ->setEmployee((new Employee())->setName('Peter')),
                    (new Shift())
                        ->setStart('2024-02-01T08:00:00')
                        ->setEnd('2024-02-01T20:00:00')
                        ->setEmployee((new Employee())->setName('Marry')),
                ],
                'expectedOccupations' => [
                    (new DayOccupation())
                        ->setDay(1)
                        ->setStartHour(0.0)
                        ->setEndHour(8.0)
                        ->setEmployee((new Employee())->setName('Peter')),
                    (new DayOccupation())
                        ->setDay(1)
                        ->setStartHour(8.0)
                        ->setEndHour(20.0)
                        ->setEmployee((new Employee())->setName('Marry')),
                ],
            ],

            'test joined shifts 1' => [
                'shifts' => [
                    (new Shift())
                        ->setStart('2024-02-01T00:00:00')
                        ->setEnd('2024-02-01T08:00:00')
                        ->setEmployee((new Employee())->setName('Peter')),
                    (new Shift())
                        ->setStart('2024-02-01T08:00:00')
                        ->setEnd('2024-02-01T20:00:00')
                        ->setEmployee((new Employee())->setName('Peter')),
                ],
                'expectedOccupations' => [
                    (new DayOccupation())
                        ->setDay(1)
                        ->setStartHour(0.0)
                        ->setEndHour(20.0)
                        ->setEmployee((new Employee())->setName('Peter')),
                ],
            ],
            'test joined shifts 2' => [
                'shifts' => [
                    (new Shift())
                        ->setStart('2024-02-01T00:00:00')
                        ->setEnd('2024-02-01T08:00:00')
                        ->setEmployee((new Employee())->setName('Peter')),
                    (new Shift())
                        ->setStart('2024-02-01T08:00:00')
                        ->setEnd('2024-02-01T20:00:00')
                        ->setEmployee((new Employee())->setName('Peter')),
                    (new Shift())
                        ->setStart('2024-02-01T20:00:00')
                        ->setEnd('2024-02-02T00:00:00')
                        ->setEmployee((new Employee())->setName('Peter')),
                ],
                'expectedOccupations' => [
                    (new DayOccupation())
                        ->setDay(1)
                        ->setStartHour(0.0)
                        ->setEndHour(24.0)
                        ->setEmployee((new Employee())->setName('Peter')),
                ],
            ],
            'test split shifts 2 (unsolvable case)' => [
                'shifts' => [
                    (new Shift())
                        ->setStart('2024-02-01T00:00:00')
                        ->setEnd('2024-02-01T08:00:00')
                        ->setEmployee((new Employee())->setName('Peter')),
                    (new Shift())
                        ->setStart('2024-02-01T08:00:00')
                        ->setEnd('2024-02-01T20:00:00')
                        ->setEmployee((new Employee())->setName('Marry')),
                    (new Shift())
                        ->setStart('2024-02-01T20:00:00')
                        ->setEnd('2024-02-02T00:00:00')
                        ->setEmployee((new Employee())->setName('Peter')),
                ],
                'expectedOccupations' => [
                    (new DayOccupation())
                        ->setDay(1)
                        // hack value
                        ->setStartHour(0.001)
                        ->setEndHour(24.0)
                        ->setEmployee((new Employee())->setName('Peter')),
                    (new DayOccupation())
                        ->setDay(1)
                        ->setStartHour(8.0)
                        ->setEndHour(20.0)
                        ->setEmployee((new Employee())->setName('Marry')),
                ],
            ],
            'test null employee' => [
                'shifts' => [
                    (new Shift())
                        ->setStart('2024-02-01T00:00:00')
                        ->setEnd('2024-02-01T08:00:00')
                        ->setEmployee((new Employee())->setName('Peter')),
                    (new Shift())
                        ->setStart('2024-02-01T08:00:00')
                        ->setEnd('2024-02-01T20:00:00')
                        ->setEmployee((new Employee())->setName('Peter')),
                    (new Shift())
                        ->setStart('2024-02-01T20:00:00')
                        ->setEnd('2024-02-02T00:00:00')
                        ->setEmployee(null),
                ],
                'expectedOccupations' => [
                    (new DayOccupation())
                        ->setDay(1)
                        ->setStartHour(0.0)
                        ->setEndHour(20.0)
                        ->setEmployee((new Employee())->setName('Peter')),
                    (new DayOccupation())
                        ->setDay(1)
                        ->setStartHour(20.0)
                        ->setEndHour(24.0)
                        ->setEmployee(null),
                ],
            ],

        ];
    }
}