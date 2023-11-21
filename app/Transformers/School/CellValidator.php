<?php

namespace App\Transformers\School;

use App\Exceptions\ValidateException;

class CellValidator
{
    const DAYS_OF_WEEK = ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'];

    public static function validateCells(array $data): bool
    {
        $requiredKeys = ['timeslotList', 'roomList', 'lessonList'];

        foreach ($requiredKeys as $requiredKey) {
            if (!array_key_exists($requiredKey, $data)) {
                throw new ValidateException(sprintf('The required key %s is missing', $requiredKey));
            }
        }

        foreach ($data['timeslotList'] as $timeslot) {
            $dayOfWeek = $timeslot['dayOfWeek'];

            if (!in_array($dayOfWeek, self::DAYS_OF_WEEK)) {
                throw new ValidateException(sprintf('Invalid day of week %s, must be one of [%s]', $dayOfWeek, join(',', self::DAYS_OF_WEEK)));
            }
        }

        return true;
    }
}