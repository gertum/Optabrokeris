<?php

namespace App\Transformers\School;

use App\Exceptions\ValidateException;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;

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

        $timeslotLine = 0;
        foreach ($data['timeslotList'] as $timeslot) {
            $dayOfWeek = $timeslot['dayOfWeek'];

            if (!in_array($dayOfWeek, self::DAYS_OF_WEEK)) {
                throw new ValidateException(sprintf('Invalid day of week %s, must be one of [%s]. Line %s.',
                    $dayOfWeek, join(',', self::DAYS_OF_WEEK), $timeslotLine));
            }

            $id = $timeslot['id'];
            if (!is_numeric($id) || intval($id) <= 0) {
                throw new ValidateException(sprintf('Invalid timeslot id [%s]. Line %s.',
                    $id, $timeslotLine));
            }

            $startTime = $timeslot['startTime'];

            $wrongStartTime = false;
            $details = '';
            try {
                $rez = Carbon::createFromFormat('H:i:s', $startTime);
                $wrongStartTime = empty($rez);
            } catch (InvalidFormatException $e) {
                $details = $e->getMessage();
                $wrongStartTime = true;
            }

            if ($wrongStartTime) {
                throw new ValidateException(sprintf('Invalid timeslot start time [%s] (%s). Line %s.',
                    $startTime, $details, $timeslotLine));
            }

            $endTime = $timeslot['endTime'];
            $wrongEndTime = false;
            $details = '';
            try {
                $rez = Carbon::createFromFormat('H:i:s', $endTime);
                $wrongEndTime = empty($rez);
            } catch (InvalidFormatException $e) {
                $details = $e->getMessage();
                $wrongEndTime = true;
            }

            if ($wrongEndTime) {
                throw new ValidateException(sprintf('Invalid timeslot end time [%s] (%s). Line %s.',
                    $endTime, $details, $timeslotLine));
            }


            $timeslotLine++;
        }

        $rooms = $data['roomList'];
        $roomLine = 0;
        foreach ($rooms as $room) {
            $id = $room['id'];
            if (!is_numeric($id) || intval($id) <= 0) {
                throw new ValidateException(sprintf('Invalid room id [%s]. Line %s.',
                    $id, $roomLine));
            }
            $roomLine++;
        }

        $lessons = $data['lessonList'];
        $lessonLine = 0;
        foreach ($lessons as $lesson) {
            $id = $lesson['id'];
            if (!is_numeric($id) || intval($id) <= 0) {
                throw new ValidateException(sprintf('Invalid lesson id [%s]. Line %s.',
                    $id, $lessonLine));
            }
            $lessonLine++;
        }

        return true;
    }
}