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
        $redCell = $xlsx->getCell(0, 'F10');
        $whiteCell = $xlsx->getCell(0, 'G14');
        $numberCell = $xlsx->getCell(0, 'G18');

        $rows = $xlsx->rowsEx(0);

        $this->assertEquals( '1970-01-01 08:00:00', $numberCell);


        $this->assertTrue(true);
    }

}