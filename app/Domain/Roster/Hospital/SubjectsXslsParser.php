<?php

namespace App\Domain\Roster\Hospital;

use App\Domain\Roster\SubjectsArray;

class SubjectsXslsParser
{
    public function parse(string $xlsxFile ) : SubjectsArray {
        $result = new SubjectsArray();
        // TODO

        // 1) parse xslx file
        // 2) find columns 'etatas' and 'darbo valandos'
        // 3) read row after row, until two empty consequite rows found
        // 4) read name, position amount, and hours in a day values and create SubjectData element; put it to results array.
        return $result;
    }
}