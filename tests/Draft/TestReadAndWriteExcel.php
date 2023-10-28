<?php

namespace Tests\Draft;

use App\Transformers\ExcelParser;
use App\Transformers\ExcelWriter;
use PHPUnit\Framework\TestCase;

class TestReadAndWriteExcel extends TestCase
{
    public function testReadAndWrite()
    {
        $file = __DIR__ . '/SchoolDataWithHeaders.xlsx';
        $rezFile = __DIR__ . '/RezSchoolDataWithHeaders.xlsx';

        $rows = ExcelParser::getSheetsRows($file, 3);

        ExcelWriter::writeSheetsRows($rezFile, $rows);

        $this->assertTrue(true);
    }

}