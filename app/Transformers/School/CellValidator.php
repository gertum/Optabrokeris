<?php

namespace App\Transformers\School;

use App\Exceptions\ValidateException;

class CellValidator
{
    public static function validateCells(array $data): bool
    {
        $requiredKeys = ['timeslotList', 'roomList', 'lessonList'];

        foreach ($requiredKeys as $requiredKey) {
            if (!array_key_exists($requiredKey, $data)) {
                throw new ValidateException(sprintf('The required key %s is missing', $requiredKey));
            }
        }

        return true;
    }
}