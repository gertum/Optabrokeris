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

    public const LT_MONTH = [
        1 => 'SAUSIS',
        2 => 'VASARIS',
        3 => 'KOVAS',
        4 => 'BALANDIS',
        5 => 'GEGUŽĖ',
        6 => 'BIRŽELIS',
        7 => 'LIEPA',
        8 => 'RUGPJŪTIS',
        9 => 'RUGSĖJIS',
        10 => 'SPALIS',
        11 => 'LAPKRITIS',
        12 => 'GRUODIS',
    ];

    public const LT_MONTH_LATINIZED = [
        1 => 'SAUSIS',
        2 => 'VASARIS',
        3 => 'KOVAS',
        4 => 'BALANDIS',
        5 => 'GEGUZE',
        6 => 'BIRZELIS',
        7 => 'LIEPA',
        8 => 'RUGPJUTIS',
        9 => 'RUGSEJIS',
        10 => 'SPALIS',
        11 => 'LAPKRITIS',
        12 => 'GRUODIS',
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

    public function setYear(int $year): DateRecognizer
    {
        $this->year = $year;
        return $this;
    }

    public function setMonth(int $month): DateRecognizer
    {
        $this->month = $month;
        return $this;
    }

    public function recognizeMonthOnly($monthName) : void {
        $ltMonthSet = array_flip(self::LT_MONTH);

        if ( array_key_exists($monthName, $ltMonthSet) ) {
            $this->month = $ltMonthSet[$monthName];
            return;
        }

        $ltLatMontSet = array_flip(self::LT_MONTH_LATINIZED);
        if ( array_key_exists($monthName, $ltLatMontSet) ) {
            $this->month = $ltLatMontSet[$monthName];
        }
    }
}