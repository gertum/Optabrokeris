<?php

namespace Tests\Unit\Roster\Write;

use App\Domain\Roster\Employee;
use App\Domain\Roster\Hospital\ScheduleParser;
use App\Domain\Roster\Hospital\ScheduleWriter;
use App\Domain\Roster\Schedule;
use App\Domain\Roster\Shift;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

/**
 * It is difficult to check xlsx files correctness.
 * We resolve this problem by parsing the xlsx file twice:
 * 1) Original xlsx file, 2) the written xlsx file .
 * Compare the result.
 */
class ParseTwiceScheduleTestSlow extends TestCase
{
    /**
     * @dataProvider provideDataForWriter
     */
    public function testParseTwiceSchedule(string $sourceXlsx, string $tmpXlsx) {
        $logger = new Logger('test');

        $scheduleParser = new ScheduleParser();
        $schedule = $scheduleParser->parseScheduleXls($sourceXlsx, ScheduleParser::createHospitalTimeSlices());

//        $destinationXlsx = tempnam('/tmp', 'tempxlsx');
        $destinationXlsx = $tmpXlsx;

        $scheduleWriter = new ScheduleWriter($logger);
        $scheduleWriter->writeSchedule($sourceXlsx, $schedule, $destinationXlsx);

        // new parser to clear in memory cache of the parser.
        $scheduleParser = new ScheduleParser();
        $schedule2 = $scheduleParser->parseScheduleXls($destinationXlsx, ScheduleParser::createHospitalTimeSlices());

        $this->assertEquals($schedule,$schedule2);
    }

    public static function provideDataForWriter(): array
    {
        return [
            'test1' => [
                'sourceXlsx' => __DIR__ . '/../data/small.xlsx',
                'tmpXlsx' => __DIR__ . '/../data/tmp_small.xlsx',
            ],
            'test vasaris' => [
                'sourceXlsx' => __DIR__ . '/../data/vasaris.xlsx',
                'tmpXlsx' => __DIR__ . '/../data/tmp_vasaris.xlsx',
            ],
            'test cleaned' => [
                'sourceXlsx' => __DIR__ . '/../data/cleaned_schedule2.xlsx',
                'tmpXlsx' => __DIR__ . '/../data/tmp_cleaned_schedule2.xlsx',
            ],
        ];
    }

}