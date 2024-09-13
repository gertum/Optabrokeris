<?php

namespace App\Domain\Roster\Hospital;

use alexandrainst\XlsxFastEditor\XlsxFastEditor;
use App\Domain\Roster\Employee;
use App\Domain\Roster\Report\ScheduleReport;
use App\Domain\Roster\Schedule;
use App\Util\MapBuilder;
use Carbon\Carbon;
use Psr\Log\LoggerInterface;

class ScheduleWriter
{
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
        $wrapper = ExcelWrapper::parse($fileTemplate);

        $eilNrTitle = $wrapper->findEilNrTitle();
        $eilNrs = $wrapper->parseEilNrs($eilNrTitle);
        $employees = $wrapper->parseEmployees($eilNrs); // employees brings row number inside

        /** @var Employee[] $employeesByName */
        $employeesByName = MapBuilder::buildMap($employees, fn(Employee $e) => $e->name);


        $this->clearAllTimings($wrapper, $eilNrTitle, $eilNrs, $xlsxFastEditor);

        foreach ($schedule->shiftList as $shift) {
            if ($shift->employee == null || $shift->employee->name == null) {
                continue;
            }

            if (!array_key_exists($shift->employee->name, $employeesByName)) {
                $this->logger->warning(
                    sprintf(
                        'Could not find employee by name [%s] in excel file [%s]',
                        $shift->employee->name,
                        $fileTemplate
                    )
                );
                continue;
            }

            $parsedEmployee = $employeesByName[$shift->employee->name];
            $row = $parsedEmployee->getRow();

            $startDate = Carbon::parse($shift->start);
            $endDate = Carbon::parse($shift->end);
            $column = $wrapper->getColumnByDay($eilNrTitle->getColumn(), $startDate->day);

            $cellFrom = $wrapper->getCell($row, $column);
            $cellTill = $wrapper->getCell($row + 1, $column);


//            $xlsxFastEditor->writeString($worksheetId1, $cellFrom->name, $startDate->format('H:i'));
//            $xlsxFastEditor->writeString($worksheetId1, $cellTill->name, $endDate->format('H:i'));
            $dayPartFrom = $startDate->hour / 24;
            $dayPartTill = $endDate->hour / 24;
            $xlsxFastEditor->writeFloat($worksheetId1, $cellFrom->name, $dayPartFrom);
            $xlsxFastEditor->writeFloat($worksheetId1, $cellTill->name, $dayPartTill);
        }

        // ================ Write summaries ==========================

        $scheduleReport = new ScheduleReport();

        $scheduleReport->fillFromSchedule($schedule, $this->logger);

        // find column for 'Darbo valandų priskirta'
        $workingHoursAssignedCell = $wrapper->findWorkingHoursAssignedTitle();
        if ($workingHoursAssignedCell != null) {
            foreach ($scheduleReport->getEmployeesInfos() as $employeeinfo) {
                if (!array_key_exists($employeeinfo->getEmployee()->name, $employeesByName)) {
                    $this->logger->error(sprintf('There is no employee by name [%s]', $employeeinfo->getEmployee()->name));
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
                $xlsxFastEditor->writeString($worksheetId1, $cell->name, "".$dayInfo->getHoursTotal());
            }
        }

        // ===================== storing results =====================
        $xlsxFastEditor->save();
    }

    /**
     * @param EilNr[] $eilNrs
     */
    public function clearAllTimings(
        ExcelWrapper $wrapper,
        EilNrTitle $eilNrTitle,
        array $eilNrs,
        XlsxFastEditor $xlsxFastEditor
    ) {
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
}