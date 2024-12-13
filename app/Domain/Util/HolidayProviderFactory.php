<?php

namespace App\Domain\Util;

class HolidayProviderFactory
{

    public function make() : HolidayProvider
    {
        $holidayProvider = new StaticHolidayProvider();

        $holidayProvider->addHoliday(1, 1, "Naujieji metai");
        $holidayProvider->addHoliday(2, 16, "Lietuvos valstybės atkūrimo diena");
        $holidayProvider->addHoliday(3, 11, "Lietuvos nepriklausomybės atkūrimo diena");
        $holidayProvider->addHoliday(4, 20, "2025 metų Velykos");
        $holidayProvider->addHoliday(5, 1, "Tarptautinė darbo diena");
        $holidayProvider->addHoliday(6, 24, "Joninės");
        $holidayProvider->addHoliday(7, 6, "Mindauginės");
        $holidayProvider->addHoliday(8, 15, "Žolinė");
        $holidayProvider->addHoliday(11, 1, "Šventieji");
        $holidayProvider->addHoliday(11, 2, "Vėlinės");
        $holidayProvider->addHoliday(12, 24, "Kūčios");
        $holidayProvider->addHoliday(12, 25, "Kalėdos");
        $holidayProvider->addHoliday(12, 26, "Kalėdos 2");

        return $holidayProvider;
    }
}