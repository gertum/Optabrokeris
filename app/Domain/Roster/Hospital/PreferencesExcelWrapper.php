<?php

namespace App\Domain\Roster\Hospital;

use App\Util\DateRecognizer;

class PreferencesExcelWrapper extends ExcelWrapper
{

    public function findYearMonth() : DateRecognizer {
        // year is at (0,0) cell
        // month is inside multiple cells (0,1)+(0,2)...+(0,n)

        $dateRecognizer = new DateRecognizer();

        return  $dateRecognizer;
    }

}