<?php

namespace App\Domain\Roster\Report;

use App\Domain\Roster\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ScheduleReport
{
    /**
     * @var DayInfo[]
     */
    private array $daysInfos = [];

    /**
     * @var Employeeinfo[]
     */
    private array $employeesInfos = [];

    public function fillFromSchedule(Schedule $schedule)
    {
        // initialize day infos and employees infos
        for ($day = 1; $day <= 31; $day++) {
            $this->daysInfos[$day] = new DayInfo($day, 0);
        }

        // initialize employees infos
        foreach ($schedule->employeeList as $employee) {
            $this->employeesInfos[$employee->name] = new Employeeinfo($employee, 0);
        }

        // collect data from shifts
        foreach ($schedule->shiftList as $shift) {
            $end = Carbon::parse($shift->end);
            $start = Carbon::parse($shift->start);

            // might be incorrect when there is part or hours
            $duration = $start->diff($end)->h;

            if ($shift->employee == null) {
                // may be should mark somehow that some shift is not occupied.
                continue;
            }
            // find employee info
            if (!array_key_exists($shift->employee->name, $this->employeesInfos)) {
                Log::error('In schedule report could not find an employee by name '.$shift->employee->name);

                continue;
            }
            if (!array_key_exists($start->day, $this->daysInfos)) {
                Log::error('In schedule report could not find a day '.$start->day);

                continue;
            }

            $this->employeesInfos[$shift->employee->name]->addHours($duration);
            $this->daysInfos[$start->day]->addHours($duration);
        }
    }

    public function getDaysInfos(): array
    {
        return $this->daysInfos;
    }

    public function getEmployeesInfos(): array
    {
        return $this->employeesInfos;
    }
}