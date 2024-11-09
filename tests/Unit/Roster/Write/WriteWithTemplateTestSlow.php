<?php

namespace Tests\Unit\Roster\Write;

use App\Domain\Roster\Employee;
use App\Domain\Roster\Hospital\ScheduleWriter;
use App\Domain\Roster\Schedule;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class WriteWithTemplateTestSlow extends TestCase
{
    /**
     * @dataProvider provideDataForWrite
     */
    public function testWrite(Schedule $schedule, string $templateFile ) {
        $logger = new Logger('test');

        $this->assertFileExists($templateFile);

        $scheduleWriter = new ScheduleWriter($logger);

        $resultFile = __DIR__.'/tmp/results_from_template.xlsx';

        $scheduleWriter->writeResultsUsingTemplate($schedule, $templateFile, $resultFile);
        $this->assertTrue(true);
        // TODO assert later by parsing additional time
    }

    public static function provideDataForWrite() : array {
        return [
            'test1' => [
                'schedule' => (new Schedule())->setEmployeeList(
                    [
                        (new Employee())->setName("Jonas Jonaitis"),
                        (new Employee())->setName("Petras Petraitis"),
                    ]
                ),
                'templateFile' => 'data/roster/template_for_roster_results.xlsx',
            ]
        ];
    }

}