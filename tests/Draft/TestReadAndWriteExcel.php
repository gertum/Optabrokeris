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

//        var_export($rows);

        ExcelWriter::writeSheetsRows($rezFile, $rows);

        $this->assertTrue(true);

        // lets also read json

        $json = file_get_contents(__DIR__.'/data.json');

        $jsonData = json_decode($json, true);

        var_export($jsonData);
    }



}