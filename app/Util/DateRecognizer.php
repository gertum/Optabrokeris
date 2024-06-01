<?php

namespace App\Util;

class DateRecognizer
{
    public const LT_BELONG_TO_MONTH = [
        'sausio',
        'vasario',
        'kovo',
        'balandžio',
        'gegužės',
        'birželio',
        'liepos',
        'rugpjūčio',
        'rugsėjo',
        'spalio',
        'lapkričio',
        'gruodžio',
    ];

    private int $year = 0;
    private int $month = 0;

    public function recognizeMonthDate(string $yearMonthRepresentation): bool
    {
        return false;
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