<?php

namespace App\Domain\Roster\Hospital;

use App\Util\DateRecognizer;

class PreferencesExcelWrapper extends ExcelWrapper
{

    public const MAX_MONTH_NAME_CELLS = 30;
    public function findYearMonth() : DateRecognizer {
        // year is at (0,0) cell
        // month is inside multiple cells (0,1)+(0,2)...+(0,n)

        $dateRecognizer = new DateRecognizer();

        $yearCell = $this->getCell(0,0)->value;
        $dateRecognizer->setYear( $yearCell );

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