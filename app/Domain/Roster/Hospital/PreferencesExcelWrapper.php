<?php

namespace App\Domain\Roster\Hospital;

use App\Exceptions\ValidateException;
use App\Util\DateRecognizer;

class PreferencesExcelWrapper extends ExcelWrapper
{

    public const MAX_MONTH_NAME_CELLS = 30;
    public function findYearMonth() : DateRecognizer {
        // year is at (0,0) cell
        // month is inside multiple cells (0,1)+(0,2)...+(0,n)

        $dateRecognizer = new DateRecognizer();

        $yearCellValue = $this->getCell(0,0)->value;
        $year = intval($yearCellValue);
        if ( $year == 0 ) {
            throw new ValidateException('Given excel does not contain correct year value');
        }

        $dateRecognizer->setYear(  $year );

        // extract month
        $collectedCells = [];
        for($column = 1; $column <= self::MAX_MONTH_NAME_CELLS; $column++) {
            $collectedCells[] = trim( $this->getCell(0,$column)->value );
        }

        $monthName = join ( '', $collectedCells );
        $dateRecognizer->recognizeMonthOnly($monthName);

        return  $dateRecognizer;
    }
}