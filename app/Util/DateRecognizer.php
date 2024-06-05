<?php

namespace App\Util;

/**
 * Recognizes date from a value received from a xlsx cell.
 */
class DateRecognizer
{
    public const LT_BELONG_TO_MONTH = [
        1 => 'sausio',
        2 => 'vasario',
        3 => 'kovo',
        4 => 'balandžio',
        5 => 'gegužės',
        6 => 'birželio',
        7 => 'liepos',
        8 => 'rugpjūčio',
        9 => 'rugsėjo',
        10 => 'spalio',
        11 => 'lapkričio',
        12 => 'gruodžio',
    ];
    private static array $monthSet = [];

    private static string $regexp = '';

    private int $year = 0;
    private int $month = 0;


    public function recognizeMonthDate(string $yearMonthRepresentation): bool
    {
        $yearMonthRepresentationLow = strtolower($yearMonthRepresentation);
        $match = preg_match($this->getRegexp(), $yearMonthRepresentationLow, $matches);

        if (!$match) {
            return false;
        }

        $this->year = $matches[1];
        $monthSet = $this->getMonthSet();
        $monthRepresentation = $matches[2];
        $this->month = $monthSet[$monthRepresentation];

        return true;
    }

    private function getMonthSet(): array
    {
        if (count(self::$monthSet) == 0) {
            self::$monthSet = array_flip(self::LT_BELONG_TO_MONTH);
        }

        return self::$monthSet;
    }

    private function getRegexp(): string
    {
        if ( self::$regexp == '' ) {
            $joinedMonths = join ( '|', self::LT_BELONG_TO_MONTH);
            self::$regexp = "/(\\d{4})\\s*m\\.\\s+(".$joinedMonths.")\\s+mėn\\.*/";
        }

        return self::$regexp;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getMonth(): int
    {
        return $this->month;
    }
}