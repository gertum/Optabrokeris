<?php

namespace App\Transformers;

use App\Exceptions\SolverDataException;
use Shuchkin\SimpleXLSX;

class ExcelParser
{
    public static function getSheetsRows(string $excelFile, int $sheetsCount): array
    {
        $xlsx = SimpleXLSX::parse($excelFile);

        if (!$xlsx) {
            throw new SolverDataException(sprintf('Wrong excel file %s', $excelFile));
        }

        $sheetsRows = [];
        for ($i = 0; $i < $sheetsCount; $i++) {
            $sheetName = $xlsx->sheetName($i);
            $sheetsRows[$sheetName] = $xlsx->rows($i);
        }

        return $sheetsRows;
    }

    public static function extractId(?string $data): ?int {
        if ( $data === null ) {
            return null;
        }

        $match = preg_match('/^\[(\d+)\].*/', $data, $matches );

        if ( !$match ) {
            return null;
        }

        return $matches[1];
    }
}