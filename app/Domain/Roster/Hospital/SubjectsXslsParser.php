<?php

namespace App\Domain\Roster\Hospital;

use App\Domain\Roster\SubjectData;
use App\Domain\Roster\SubjectsContainer;
use App\Exceptions\ExcelParseException;
use Carbon\Carbon;

class SubjectsXslsParser
{
    public function parse(string $xlsxFile): SubjectsContainer
    {
        $result = new SubjectsContainer();

        // 1) parse xslx file
        $wrapper = ExcelWrapper::parse($xlsxFile);

        // 2) find columns 'etatas' and 'darbo valandos'

        $positionAmountMatcher = new CustomValueCellMatcher('/etatas/');
        $workingHoursMatcher = new CustomValueCellMatcher('/darbo valandos/');

        $wrapper->registerMatcher('positionAmount', $positionAmountMatcher);
        $wrapper->registerMatcher('workingHours', $workingHoursMatcher);
        $wrapper->runMatchers();

        if ($positionAmountMatcher->getColumn() < 0) {
            throw new ExcelParseException('Nepavyko rasti etatų stulpelio');
        }
        if ($workingHoursMatcher->getColumn() < 0) {
            throw new ExcelParseException('Nepavyko rasti darbo valandų stulpelio');
        }
        // 3) read row after row, until two empty consequite rows found
        $subjectsColumn = 0;
        $subjectsRow = $positionAmountMatcher->getRow() + 1;

        // 4) read name, position amount, and hours in a day values and create SubjectData element; put it to results array.
        /** @var SubjectData[] $subjects */
        $subjects = [];
        $emptyConsequitiveRows = 0;
        while ($subjectsRow < $wrapper->getMaxRows()) {
            $subjectCell = $wrapper->getCell($subjectsRow, $subjectsColumn);
            $positionAmountCell = $wrapper->getCell($subjectsRow, $positionAmountMatcher->getColumn());
            $workingHoursCell = $wrapper->getCell($subjectsRow, $workingHoursMatcher->getColumn());

            $subjectsRow++;
            if ($subjectCell->value == '') {
                $emptyConsequitiveRows++;
                if ($emptyConsequitiveRows >= 3) {
                    break;
                }
                continue;
            }

            $emptyConsequitiveRows = 0;

            $hour = 8;
            $minute = 0;
            if ($workingHoursCell->value != null) {
                $workingHoursObject = Carbon::create($workingHoursCell->value);

                $hour = $workingHoursObject->hour;
                $minute = $workingHoursObject->minute;
            }

            $positionAmount = floatval($positionAmountCell->value);
            if ($positionAmount == 0) {
                $positionAmount = 1;
            }
            $subjects[] = (new SubjectData())
                ->setName($subjectCell->value)
                ->setPositionAmount($positionAmount)
                ->setHoursInDay($hour + (float)$minute / 60)
            ;
        }

        $result->setSubjects($subjects);

        return $result;
    }
}