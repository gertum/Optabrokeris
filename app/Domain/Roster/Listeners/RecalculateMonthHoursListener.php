<?php

namespace App\Domain\Roster\Listeners;

use App\Domain\Roster\Events\BeforeApplyingSubjectsToScheduleEvent;
use App\Domain\Util\HolidayProviderFactory;
use App\Util\WorkingDaysCalculator;
use Illuminate\Support\Facades\Log;

class RecalculateMonthHoursListener
{
    private HolidayProviderFactory $holidayProviderFactory;

    /**
     * @param HolidayProviderFactory $holidayProviderFactory
     */
    public function __construct(HolidayProviderFactory $holidayProviderFactory)
    {
        $this->holidayProviderFactory = $holidayProviderFactory;
    }


    public function handle(BeforeApplyingSubjectsToScheduleEvent $event): void
    {
        Log::debug('Recalculating month hours.');

        $holidayProvider = $this->holidayProviderFactory->make();

        $monthDate = $event->getSchedule()->detectMonthDate();

        $workingDaysInMonth = WorkingDaysCalculator::calculateWorkingDaysInMonth( $monthDate->year, $monthDate->month, $holidayProvider, [1,2,3,4,5] );

        foreach ($event->getSubjects() as $subject) {
            $subject->setHoursInMonth($subject->getHoursInDay() * $workingDaysInMonth);
        }
    }
}