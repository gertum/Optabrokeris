<?php

namespace App\Transformers;

use Shuchkin\SimpleXLSXGen;

class ExcelWriter
{
    public static function writeSheetsRows(string $excelFile, array $sheetsRows): void
    {
        $xlsx = new SimpleXLSXGen();

        foreach ($sheetsRows as $sheetName => $rows) {
            $xlsx->addSheet($rows, $sheetName);
        }

        $xlsx->saveAs($excelFile);
    }
}