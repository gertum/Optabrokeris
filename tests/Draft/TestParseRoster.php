<?php

namespace Draft;

use App\Transformers\ExcelParser;
use PHPUnit\Framework\TestCase;
use Shuchkin\SimpleXLSX;

class TestParseRoster extends TestCase
{
    public function testParse() {

        $excelFile = __DIR__."/data/vasaris.xlsx";
        $this->assertTrue( file_exists( $excelFile) );

        $xlsx = SimpleXLSX::parse($excelFile);

        // check the red square and the time square

        $this->assertTrue(true);
    }

}