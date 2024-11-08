<?php

namespace App\Domain\Roster\Hospital;

use App\Domain\Roster\SubjectsContainer;

class SubjectsCsvParser
{
    public function parse(string $csvFile ) : SubjectsContainer {
        $result = new SubjectsContainer();
        // TODO

        return $result;
    }
}