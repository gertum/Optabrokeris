<?php

namespace Tests\Unit\Roster\Write;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Employee;
use App\Domain\Roster\Hospital\ScheduleParser;
use App\Domain\Roster\Hospital\ScheduleWriter;
use App\Domain\Roster\Hospital\ShiftsBuilder;
use App\Domain\Roster\Profile;
use App\Domain\Roster\Schedule;
use App\Domain\Roster\Shift;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class WriteWithTemplateTestSlow extends TestCase
{
    /**
     * @dataProvider provideDataForWrite
     */
    public function testWrite(Schedule $schedule, string $templateFile, Profile $profile)
    {
        $logger = new Logger('test');

        $this->assertFileExists($templateFile);

        $scheduleWriter = new ScheduleWriter($logger);

        $resultFile = __DIR__ . '/tmp/results_from_template.xlsx';

        $scheduleWriter->writeResultsUsingTemplate($schedule, $templateFile, $resultFile);
        $this->assertTrue(true);

        $scheduleParser = new ScheduleParser();
        $timeSlices = ShiftsBuilder::transformBoundsToTimeSlices($profile->getShiftBounds());
        $schedule2 = $scheduleParser->parseScheduleXls($resultFile, $timeSlices);

        $this->assertEquals($schedule->getEmployeesNames(), $schedule2->getEmployeesNames());
        // TODO assert the particular availability and a particular shift
    }

    public static function provideDataForWrite(): array
    {
        return [
            'test1' => [
                'schedule' => (new Schedule())->setEmployeeList(
                    [
                        (new Employee())->setName("Jonas Jonaitis")
                            ->setMaxWorkingHours(75)
                            ->setWorkingHoursPerDay(7.5)
                            ->setPositionAmount(0.9)
                        ,

                        (new Employee())->setName("Petras Petraitis")
                            ->setMaxWorkingHours(50)
                            ->setWorkingHoursPerDay(3.5)
                            ->setPositionAmount(0.45)
                        ,
                    ]
                )
                    ->setAvailabilityList(
                        [
                            (new Availability())
                                ->setDate('2024-10-30T20:00:00')
                                ->setDateTill('2024-11-01T08:00:00')
                                ->setAvailabilityType(Availability::AVAILABLE)
                                ->setEmployee((new Employee())->setName("Jonas Jonaitis")),
                            (new Availability())
                                ->setDate('2024-11-01T08:00:00')
                                ->setDateTill('2024-11-01T20:00:00')
                                ->setAvailabilityType(Availability::AVAILABLE)
                                ->setEmployee((new Employee())->setName("Jonas Jonaitis")),
                            (new Availability())
                                ->setDate('2024-11-01T20:00:00')
                                ->setDateTill('2024-11-02T08:00:00')
                                ->setAvailabilityType(Availability::AVAILABLE)
                                ->setEmployee((new Employee())->setName("Jonas Jonaitis")),
                            (new Availability())
                                ->setDate('2024-11-02T08:00:00')
                                ->setDateTill('2024-11-02T20:00:00')
                                ->setAvailabilityType(Availability::UNAVAILABLE)
                                ->setEmployee((new Employee())->setName("Jonas Jonaitis")),
                            // ---
                            (new Availability())
                                ->setDate('2024-10-30T20:00:00')
                                ->setDateTill('2024-11-01T08:00:00')
                                ->setAvailabilityType(Availability::UNAVAILABLE)
                                ->setEmployee((new Employee())->setName("Petras Petraitis")),
                            (new Availability())
                                ->setDate('2024-11-01T08:00:00')
                                ->setDateTill('2024-11-01T20:00:00')
                                ->setAvailabilityType(Availability::UNAVAILABLE)
                                ->setEmployee((new Employee())->setName("Petras Petraitis")),
                            (new Availability())
                                ->setDate('2024-11-01T20:00:00')
                                ->setDateTill('2024-11-02T08:00:00')
                                ->setAvailabilityType(Availability::AVAILABLE)
                                ->setEmployee((new Employee())->setName("Petras Petraitis")),
                            (new Availability())
                                ->setDate('2024-11-02T08:00:00')
                                ->setDateTill('2024-11-02T20:00:00')
                                ->setAvailabilityType(Availability::AVAILABLE)
                                ->setEmployee((new Employee())->setName("Petras Petraitis")),
                            (new Availability())
                                ->setDate('2024-11-02T20:00:00')
                                ->setDateTill('2024-11-03T08:00:00')
                                ->setAvailabilityType(Availability::UNAVAILABLE)
                                ->setEmployee((new Employee())->setName("Petras Petraitis")),
                        ]
                    )
                    ->setShiftList(
                        [
                            (new Shift())
                                ->setStart('2024-10-30T20:00:00')
                                ->setEnd('2024-11-01T08:00:00')
                                ->setEmployee((new Employee())->setName("Petras Petraitis")),
                            (new Shift())
                                ->setStart('2024-11-01T08:00:00')
                                ->setEnd('2024-11-01T20:00:00')
                                ->setEmployee((new Employee())->setName("Jonas Jonaitis")),
                            (new Shift())
                                ->setStart('2024-11-02T20:00:00')
                                ->setEnd('2024-11-03T08:00:00')
                                ->setEmployee((new Employee())->setName("Petras Petraitis")),
                        ]
                    )
                ,
                'templateFile' => 'data/roster/template_for_roster_results.xlsx',
                'profile' => (new Profile())->setShiftBounds([8, 20])
            ]
        ];
    }

}