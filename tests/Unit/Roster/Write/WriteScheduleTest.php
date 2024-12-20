<?php

namespace Tests\Unit\Roster\Write;

use App\Domain\Roster\Employee;
use App\Domain\Roster\Hospital\ScheduleParser;
use App\Domain\Roster\Hospital\ScheduleWriter;
use App\Domain\Roster\Schedule;
use App\Domain\Roster\Shift;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class WriteScheduleTest extends TestCase
{
    /**
     * @dataProvider provideDataForWriter
     */
    public function testWrite(string $sourceXlsx, Schedule $schedule, string $destinationXlsx, Shift $expectedShift)
    {
        $logger = new Logger('test');
        $scheduleWriter = new ScheduleWriter($logger);
        $scheduleWriter->writeSchedule($sourceXlsx, $schedule, $destinationXlsx);

        // asserts
        $scheduleParser = new ScheduleParser();

        $targetSchedule = $scheduleParser->parseScheduleXls($destinationXlsx);
        $foundShift = $targetSchedule->findShiftByStartDate($expectedShift->start);

        $this->assertEquals($expectedShift->start, $foundShift->start);
        $this->assertEquals($expectedShift->end, $foundShift->end);
        $this->assertEquals($expectedShift->employee->name, $foundShift->employee->name);

        // TODO test by checking excel cell
    }

    public static function provideDataForWriter(): array
    {
        return [
            'test1' => [
                'sourceXlsx' => __DIR__ . '/../data/small.xlsx',
                'schedule' => (new Schedule())->setShiftList([
                    (new Shift())
                        ->setStart('2024-06-01T00:00:00')
                        ->setEnd('2024-06-01T08:00:00')
                        ->setEmployee((new Employee())->setName('Aleksandras Briedis 24/12'))
                ]),
                'destinationXlsx' => __DIR__ . '/tmp/smallfilled.xlsx',
                'expectedShift' =>
                    (new Shift())
                        ->setStart('2024-06-01T00:00:00')
                        ->setEnd('2024-06-01T08:00:00')
                        ->setEmployee((new Employee())->setName('Aleksandras Briedis 24/12'))
            ],
//            'test other must be cleared' => [
//                'sourceXlsx' => __DIR__ . '/../data/small.xlsx',
//                'schedule' => (new Schedule())->setShiftList([
//                    (new Shift())
//                        ->setStart('2024-06-01T00:00:00')
//                        ->setEnd('2024-06-01T08:00:00')
//                        ->setEmployee((new Employee())->setName('Aleksandras Briedis 24/12'))
//                ]),
//                'destinationXlsx' => __DIR__ . '/tmp/smallfilled.xlsx',
//                'expectedShift' =>
//                    (new Shift())
//                        ->setStart('2024-06-01T00:00:00')
//                        ->setEnd('2024-06-01T08:00:00')
//                        ->setEmployee((new Employee())->setName('Renata Juknevičienė 29/12'))
//            ]

        ];
    }
}