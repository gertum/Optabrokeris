<?php

namespace App\Domain\Roster\Hospital;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Schedule;
use Carbon\Carbon;
use DateInterval;
use DateTimeInterface;

class ScheduleParser
{

    /**
     * @param DateInterval[] $timeSlices
     */
    public function parseScheduleXls(string $file, array $timeSlices): Schedule
    {
        $schedule = new Schedule();

        $wrapper = ExcelWrapper::parse($file);

        $eilNrTitle = $wrapper->findEilNrTitle();
        $eilNrs = $wrapper->parseEilNrs($eilNrTitle);
        $employees = $wrapper->parseEmployees($eilNrs);

        $dateRecognizer = $wrapper->findYearMonth();

        $availabilities = $wrapper->parseAvailabilities(
            $eilNrs,
            $employees,
            $dateRecognizer->getYear(),
            $dateRecognizer->getMonth()
        );

        $availabilitiesFlat = array_reduce(
            $availabilities,
            fn($flatList, $availabilitiesSubList) => array_merge($flatList, $availabilitiesSubList),
            []
        );

        $schedule->setEmployeeList($employees);
        $schedule->setAvailabilityList($availabilitiesFlat);

        $dateFrom = Carbon::create($dateRecognizer->getYear(), $dateRecognizer->getMonth())->toImmutable();

        $maxAvailabilityDate = array_reduce(
            $availabilitiesFlat,
            fn(DateTimeInterface $locallyMaxDate, Availability $a) => max($locallyMaxDate, $a->date),
            $dateFrom
        );

        /** @var Carbon $dateTill */
        $dateTill = Carbon::createFromInterface($maxAvailabilityDate);
        $dateTill->setTime(24, 0);

        $shifts = ShiftsBuilder::buildShifts($dateFrom, $dateTill->toImmutable(), $timeSlices);

        $schedule->setShiftList($shifts);

        // TODO read already written time assignment

        return $schedule;
    }

    /**
     * @return DateInterval[]
     */
    public static function createHospitalTimeSlices(): array
    {
        return [
            new DateInterval('PT8H'),
            new DateInterval('PT12H'),
            new DateInterval('PT4H'),
        ];
    }
}