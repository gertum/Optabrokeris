<?php

namespace App\Domain\Roster\Hospital;

use alexandrainst\XlsxFastEditor\XlsxFastEditor;
use App\Domain\Roster\Availability;
use App\Domain\Roster\Employee;
use App\Domain\Roster\Hospital\Write\DayOccupation;
use App\Domain\Roster\Hospital\Write\ShiftsListTransformer;
use App\Domain\Roster\Report\ScheduleReport;
use App\Domain\Roster\Schedule;
use App\Exceptions\ExcelParseException;
use App\Exceptions\SolverDataException;
use App\Exceptions\ValidateException;
use App\Util\DateRecognizer;
use App\Util\Grouper;
use App\Util\MapBuilder;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Psr\Log\LoggerInterface;

class ScheduleWriter
{
    const EPSILON = 0.0001;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function writeSchedule(string $fileTemplate, Schedule $schedule, string $outputFile): void
    {
        copy($fileTemplate, $outputFile);

        $xlsxFastEditor = new XlsxFastEditor($outputFile);
        $worksheetId1 = 1;

        // $wrapper must tell where is the cell we need to edit
        try {
            $wrapper = ExcelWrapper::parse($fileTemplate);
        } catch (ExcelParseException $e) {
            throw new ValidateException('Uploaded file or template is not a valid xlsx file.');
        }

        $eilNrTitle = $wrapper->findEilNrTitle();
        $eilNrs = $wrapper->parseEilNrs($eilNrTitle);
        $employees = $wrapper->parseEmployees($eilNrs); // employees brings row number inside

        /** @var Employee[] $employeesByName */
        $employeesByName = MapBuilder::buildMap($employees, fn(Employee $e) => $e->name);


        $this->clearAllTimings($wrapper, $eilNrTitle, $eilNrs, $xlsxFastEditor);

        $occupations = ShiftsListTransformer::transform($schedule->shiftList);

//        $flagNewVersion = true;

//        if ($flagNewVersion) {
        foreach ($occupations as $occupation) {
            if ($occupation->getEmployee() == null || $occupation->getEmployee()->name == null) {
                continue;
            }

            if (!array_key_exists($occupation->getEmployee()->name, $employeesByName)) {
                $this->logger->warning(
                    sprintf(
                        'Could not find employee by name [%s] in excel file [%s]',
                        $occupation->getEmployee()->name,
                        $fileTemplate
                    )
                );
                continue;
            }

            $parsedEmployee = $employeesByName[$occupation->getEmployee()->name];
            $row = $parsedEmployee->getRow();

            $column = $wrapper->getColumnByDay($eilNrTitle->getColumn(), $occupation->getDay());

            $cellFrom = $wrapper->getCell($row, $column);
            $cellTill = $wrapper->getCell($row + 1, $column);

            $xlsxFastEditor->writeFloat($worksheetId1, $cellFrom->name, $occupation->getStartHour() / 24);
            $xlsxFastEditor->writeFloat($worksheetId1, $cellTill->name, $occupation->getEndHour() / 24);
        }
//        }
//        else {
//// =========================== old version ==========================================================
//            // TODO remove the old version code
//            foreach ($schedule->shiftList as $shift) {
//                if ($shift->employee == null || $shift->employee->name == null) {
//                    continue;
//                }
//
//                if (!array_key_exists($shift->employee->name, $employeesByName)) {
//                    $this->logger->warning(
//                        sprintf(
//                            'Could not find employee by name [%s] in excel file [%s]',
//                            $shift->employee->name,
//                            $fileTemplate
//                        )
//                    );
//                    continue;
//                }
//
//                $parsedEmployee = $employeesByName[$shift->employee->name];
//                $row = $parsedEmployee->getRow();
//
//                $startDate = Carbon::parse($shift->start);
//                $endDate = Carbon::parse($shift->end);
//                $column = $wrapper->getColumnByDay($eilNrTitle->getColumn(), $startDate->day);
//
//                $cellFrom = $wrapper->getCell($row, $column);
//                $cellTill = $wrapper->getCell($row + 1, $column);
//
//
//                $dayPartFrom = $startDate->hour / 24;
//                $dayPartTill = $endDate->hour / 24;
//                $xlsxFastEditor->writeFloat($worksheetId1, $cellFrom->name, $dayPartFrom);
//                $xlsxFastEditor->writeFloat($worksheetId1, $cellTill->name, $dayPartTill);
//            }
//            // ================== end of old version ================================================
//        }


        // ================ Write summaries ==========================

        $scheduleReport = new ScheduleReport();

        $scheduleReport->fillFromSchedule($schedule, $this->logger);

        // find column for 'Darbo valandų priskirta'
        $workingHoursAssignedCell = $wrapper->findWorkingHoursAssignedTitle();
        if ($workingHoursAssignedCell != null) {
            foreach ($scheduleReport->getEmployeesInfos() as $employeeinfo) {
                if (!array_key_exists($employeeinfo->getEmployee()->name, $employeesByName)) {
                    $this->logger->error(
                        sprintf('There is no employee by name [%s]', $employeeinfo->getEmployee()->name)
                    );
                    continue;
                }
                $parsedEmployee = $employeesByName[$employeeinfo->getEmployee()->name];

                $row = $parsedEmployee->getRow();
                $column = $workingHoursAssignedCell->column;

                $cell = $wrapper->getCell($row, $column);

                $xlsxFastEditor->writeFloat($worksheetId1, $cell->name, $employeeinfo->getHoursTotal());
            }
        }

        // find row for 'Dienos sumos:'
        $daySumsCell = $wrapper->findDaySumsTitle();
        if ($daySumsCell != null) {
            // write days sums
            foreach ($scheduleReport->getDaysInfos() as $dayInfo) {
                $column = $daySumsCell->column + $dayInfo->getDay();
                $row = $daySumsCell->row;

                $cell = $wrapper->getCell($row, $column);
                $xlsxFastEditor->writeString($worksheetId1, $cell->name, "" . $dayInfo->getHoursTotal());
            }
        }

        // ===================== storing results =====================
        $xlsxFastEditor->save();
    }

    /**
     * @param EilNr[] $eilNrs
     */
    public function clearAllTimings(
        ExcelWrapper   $wrapper,
        EilNrTitle     $eilNrTitle,
        array          $eilNrs,
        XlsxFastEditor $xlsxFastEditor
    )
    {
        $worksheetId1 = 1;

        foreach ($eilNrs as $eilNr) {
            $row = $eilNr->getRow();
            for ($day = 1; $day <= 31; $day++) {
                $column = $wrapper->getColumnByDay($eilNrTitle->getColumn(), $day);

                $cellFrom = $wrapper->getCell($row, $column);
                $cellTill = $wrapper->getCell($row + 1, $column);

                $xlsxFastEditor->writeString($worksheetId1, $cellFrom->name, "");
                $xlsxFastEditor->writeString($worksheetId1, $cellTill->name, "");
            }
        }
    }

    public function writeResultsUsingTemplate(Schedule $schedule, string $fileTemplate, string $outputFile)
    {
        // detect month date
        $monthDate = $schedule->detectMonthDate();

        if ($monthDate == null) {
            throw new SolverDataException(sprintf('Could not detect month date when writing schedule to file %s', $outputFile));
        }

        copy($fileTemplate, $outputFile);

        // $wrapper must tell where is the cell we need to edit
        $wrapper = ExcelWrapper::parse($fileTemplate);

        ScheduleParser::registerStandardMatchers($wrapper);

        $wrapper->runMatchers();

        $spreadsheet = IOFactory::load($outputFile);
        $sheet = $spreadsheet->getActiveSheet();

        $scheduleReport = new ScheduleReport();

        $scheduleReport->fillFromSchedule($schedule, $this->logger);

        $occupations = ShiftsListTransformer::transform($schedule->shiftList);

        $this->writeHeaderDate($sheet, $wrapper, $monthDate);
        $this->writeEmployees($sheet, $wrapper, $schedule, $scheduleReport);
        $this->markWeekends($sheet, $wrapper, $schedule, $monthDate);
        $this->putGreenSeparator($sheet, $wrapper, $schedule, $monthDate);
        $this->writeAvailabilities($sheet, $wrapper, $schedule, $monthDate);
        $this->writeAssignedShifts($sheet, $wrapper, $schedule, $monthDate, $occupations);
        $this->writeSummaries($sheet, $wrapper, $schedule, $monthDate, $occupations);

        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $writer->save($outputFile);
    }

    function setCellColor(Worksheet $worksheet, string $cells, string $color)
    {
        $worksheet->getStyle($cells)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB($color);
    }

    private function writeEmployees(Worksheet $sheet, ExcelWrapper $wrapper, Schedule $schedule, ScheduleReport $scheduleReport)
    {
        $eilNrMatcher = $wrapper->getMatcher('eilNr');
        $workingHoursPerDayMatcher = $wrapper->getMatcher('workingHoursPerDay');
        $positionAmountMatcher = $wrapper->getMatcher('positionAmount');
        $workingHoursPerMonthMatcher = $wrapper->getMatcher('workingHoursPerMonth');
        $assignedHoursMatcher = $wrapper->getMatcher('assignedHours');

        $row = $eilNrMatcher->getRow() + 2;
        $nr = 1;
        foreach ($schedule->employeeList as $employee) {
            $eilnrCell = $wrapper->getCell($row, $eilNrMatcher->getColumn());
            $nameCell = $wrapper->getCell($row, $eilNrMatcher->getColumn() + 1);
            $workingHOursPerDayCell = $wrapper->getCell($row, $workingHoursPerDayMatcher->getColumn());
            $positionAmountCell = $wrapper->getCell($row, $positionAmountMatcher->getColumn());
            $workingHoursPerMonthCell = $wrapper->getCell($row, $workingHoursPerMonthMatcher->getColumn());
            $assignedHoursCell = $wrapper->getCell($row, $assignedHoursMatcher->getColumn());

            $sheet->setCellValue($eilnrCell->name, $nr++);
            $sheet->setCellValue($nameCell->name, $employee->name);
            $sheet->setCellValueExplicit($workingHOursPerDayCell->name, $employee->getWorkingHoursPerDayFormatted(), DataType::TYPE_STRING);
            $sheet->setCellValue($positionAmountCell->name, $employee->getPositionAmountFormatted());
            $sheet->setCellValue($workingHoursPerMonthCell->name, $employee->getMaxWorkingHours());

            $employeeInfo = $scheduleReport->findEmployeeInfo($employee->name);
            $sheet->setCellValue($assignedHoursCell->name, $employeeInfo->getHoursTotal());

            $row += 2;
        }
    }


    // should be called before marking days
    private function markWeekends(Worksheet $worksheet, ExcelWrapper $wrapper, Schedule $schedule, Carbon $monthDate)
    {
        $monthDaysMatcher = $wrapper->getMatcher('monthDays');

        for ($day = 1; $day <= $monthDate->daysInMonth; $day++) {
            $dayDate = Carbon::create($monthDate->year, $monthDate->month, $day);
            if (in_array($dayDate->weekday(), [0, 6])) {
                $column = $monthDaysMatcher->getColumn() + $day - 1;
                $row = $monthDaysMatcher->getRow();
                for ($i = 0; $i <= count($schedule->employeeList); $i++) {
                    // 0 - sunday, 6 - saturday
                    $cell1 = $wrapper->getCell($row, $column);
                    $cell2 = $wrapper->getCell($row + 1, $column);
                    $range = $cell1->name . ':' . $cell2->name;

                    $this->setCellColor($worksheet, $range, ExcelWrapper::WEEKEND_BACKGROUND_UNHASHED);
                    $row += 2;
                }
            }
        }
    }

    private function putGreenSeparator(
        Worksheet    $worksheet,
        ExcelWrapper $wrapper,
        Schedule     $schedule,
        Carbon       $monthDate
    )
    {
        if ($monthDate->daysInMonth == 31) {
            // do nothing
            return;
        }
        $monthDaysMatcher = $wrapper->getMatcher('monthDays');
        $column = $monthDaysMatcher->getColumn() + $monthDate->daysInMonth;
        $row = $monthDaysMatcher->getRow();

        foreach ($schedule->employeeList as $employee) {
            $row += 2;

            $cell1 = $wrapper->getCell($row, $column);
            $cell2 = $wrapper->getCell($row + 1, $column);
            $range = $cell1->name . ':' . $cell2->name;
            $this->setCellColor($worksheet, $range, ExcelWrapper::SEPARATOR_BACKGROUND_UNHASHED);
        }
    }

    private function writeAvailabilities(
        Worksheet    $sheet,
        ExcelWrapper $wrapper,
        Schedule     $schedule,
        Carbon       $monthDate
    )
    {
        // find first cell of the availabilities table
        $monthDaysMatcher = $wrapper->getMatcher('monthDays');

        // for each employee
        // depending on the month date fill table with availability colors
        // iterate given month days
        // search corresponding availabilities for each day
        // mark availability with the preselected color

//        $groupedSchedule = new GroupedSchedule();
//        $groupedSchedule->importSchedule($schedule);

        $schedule->referenceEmployeesToAvailabilities();
        $schedule->assignEmployeesSequenceNumbers();
        $schedule->sortAvailabilities();


        $row = $monthDaysMatcher->getRow();
        foreach ($schedule->employeeList as $employee) {
            $row += 2;
            for ($day = 1; $day <= $monthDate->daysInMonth; $day++) {
                $column = $monthDaysMatcher->getColumn() + $day - 1;


                $nightDate = Carbon::create($monthDate->year, $monthDate->month, $day, 20, 1);
                $dayDate = Carbon::create($monthDate->year, $monthDate->month, $day, 8, 1);
                $dayDateFormatted = $dayDate->format(Schedule::TARGET_DATE_FORMAT);
                $nightDateFormatted = $nightDate->format(Schedule::TARGET_DATE_FORMAT);

                $availability = $schedule->findAvailability($employee->getKey(), $dayDateFormatted, true);
                $availabilityNight = $schedule->findAvailability($employee->getKey(), $nightDateFormatted, true);
                if ($availability == null) {
                    $availability = (new Availability())->setAvailabilityType(Availability::UNAVAILABLE);
                }
                if ($availabilityNight == null) {
                    $availabilityNight = (new Availability())->setAvailabilityType(Availability::UNAVAILABLE);
                }


                $cell1 = $wrapper->getCell($row, $column);
                $cell2 = $wrapper->getCell($row + 1, $column);
                $range = $cell1->name . ':' . $cell2->name;

                if ($availability->availabilityType == Availability::UNAVAILABLE
                    && $availabilityNight->availabilityType == Availability::UNAVAILABLE
                ) {
                    $this->setCellColor($sheet, $range, ExcelWrapper::UNAVAILABLE_BACKGROUND_UNHASHED);
                }
                if ($availability->availabilityType == Availability::UNAVAILABLE
                    && $availabilityNight->availabilityType != Availability::UNAVAILABLE
                ) {
                    $this->setCellColor($sheet, $cell1->name, ExcelWrapper::UNAVAILABLE_DAY_BACKGROUND_UNHASHED);
                }
                if ($availability->availabilityType != Availability::UNAVAILABLE
                    && $availabilityNight->availabilityType == Availability::UNAVAILABLE
                ) {
                    $this->setCellColor($sheet, $cell2->name, ExcelWrapper::UNAVAILABLE_NIGHT_BACKGROUND_UNHASHED);
                }


                if ($availability->availabilityType == Availability::DESIRED) {
                    $this->setCellColor($sheet, $cell1->name, ExcelWrapper::DESIRED_BACGROUND_UNHASHED);
                }
                if ($availabilityNight->availabilityType == Availability::DESIRED) {
                    $this->setCellColor($sheet, $cell2->name, ExcelWrapper::DESIRED_BACGROUND_UNHASHED);
                }

            }
        }

    }

    private function writeAssignedShifts(
        Worksheet    $worksheet,
        ExcelWrapper $wrapper,
        Schedule     $schedule,
        Carbon       $monthDate,
        array $occupations
    )
    {
        $eilNrMatcher = $wrapper->getMatcher('eilNr');

        // assign rows to employees
        $row = $eilNrMatcher->getRow();
        foreach ($schedule->employeeList as $employee) {
            $row += 2;
            $employee->setRow($row);
        }

        /** @var Employee[] $employeesByName */
        $employeesByName = MapBuilder::buildMap($schedule->employeeList, fn(Employee $e) => $e->name);

//        $occupations = ShiftsListTransformer::transform($schedule->shiftList);

        foreach ($occupations as $occupation) {

            if ($occupation->getStartTime()->month != $monthDate->month) {
                continue;
            }

            if ($occupation->getEmployee() == null || $occupation->getEmployee()->name == null) {
                continue;
            }

            if (!array_key_exists($occupation->getEmployee()->name, $employeesByName)) {
                $this->logger->warning(
                    sprintf(
                        'Could not find employee by name [%s]',
                        $occupation->getEmployee()->name
                    )
                );
                continue;
            }

            $foundEmployee = $employeesByName[$occupation->getEmployee()->name];
            $row = $foundEmployee->getRow();

            $column = $wrapper->getColumnByDay($eilNrMatcher->getColumn(), $occupation->getDay());

            $cellFrom = $wrapper->getCell($row, $column);
            $cellTill = $wrapper->getCell($row + 1, $column);

            $worksheet->getCell($cellFrom->name)->setValueExplicit($occupation->getStartHour() / 24 + self::EPSILON, DataType::TYPE_NUMERIC);
            $worksheet->getCell($cellTill->name)->setValueExplicit($occupation->getEndHour() / 24 + self::EPSILON, DataType::TYPE_NUMERIC);
        }
    }

    private function writeHeaderDate(
        Worksheet    $worksheet,
        ExcelWrapper $wrapper,
        Carbon       $monthDate
    )
    {
        $headerDateMatcher = $wrapper->getMatcher('datePlaceholder');
        if ($headerDateMatcher == null) {
            throw new ExcelParseException('could not find the header date placeholder when writing schedule to xlsx file');
        }
        $monthName = ucfirst(DateRecognizer::LT_BELONG_TO_MONTH[$monthDate->month]);
        $value = sprintf('%sm. %s mėn.', $monthDate->year, $monthName);
        $cell = $wrapper->getCell($headerDateMatcher->getRow(), $headerDateMatcher->getColumn());
        $worksheet->setCellValue($cell->name, $value);
    }

    /**
     * @param DayOccupation[] $occupations
     */
    private function writeSummaries(
        Worksheet    $worksheet,
        ExcelWrapper $wrapper,
        Schedule     $schedule,
        Carbon       $monthDate,
        array $occupations
    )
    {
        $eilNrMatcher = $wrapper->getMatcher('eilNr');
        $row = $eilNrMatcher->getRow() + 2 + count($schedule->employeeList) * 2;
        $nameCell = $wrapper->getCell($row, $eilNrMatcher->getColumn() + 1);

        // TODO patikslinti labelį
        $worksheet->setCellValue($nameCell->name, "Suma kiekvieną parą");
        $monthDaysMatcher = $wrapper->getMatcher('monthDays');

        /** @var DayOccupation[][] $groupedOccupations */
        $groupedOccupations = Grouper::group($occupations, fn(DayOccupation $o) => $o->getDateFormatted());

        foreach ($groupedOccupations as $oGroup) {
            if ( $oGroup[0]->getStartTime()->month != $monthDate->month) {
                continue;
            }

            $durations = array_map ( fn($o)=>$o->getEndHour() - $o->getStartHour() , $oGroup);
            $sum = array_reduce($durations, fn($sum, $value)=> $sum+$value,0);

            $column = $monthDaysMatcher->getColumn() + $oGroup[0]->getDay() - 1;
            $daySumColumn = $wrapper->getCell($row, $column);
            $worksheet->setCellValueExplicit($daySumColumn->name, $sum, DataType::TYPE_STRING );
        }
    }
}