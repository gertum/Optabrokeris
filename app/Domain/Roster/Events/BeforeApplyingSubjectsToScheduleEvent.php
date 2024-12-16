<?php

namespace App\Domain\Roster\Events;

use App\Domain\Roster\Schedule;
use App\Domain\Roster\SubjectDataInterface;
use Illuminate\Foundation\Events\Dispatchable;

class BeforeApplyingSubjectsToScheduleEvent
{
    use Dispatchable
//        , InteractsWithSockets, SerializesModels
        ;

    /**
     * @var SubjectDataInterface[]
     */
    private array $subjects;
    private Schedule $schedule;

    /**
     * @param SubjectDataInterface[] $subjects
     * @param Schedule $schedule
     */
    public function __construct(array $subjects, Schedule $schedule)
    {
        $this->subjects = $subjects;
        $this->schedule = $schedule;
    }

    public function getSubjects(): array
    {
        return $this->subjects;
    }

    public function getSchedule(): Schedule
    {
        return $this->schedule;
    }
}