<?php

namespace Draft;

use App\Fixed\FixedSimpleXLSX;
use App\Fixed\FixedSimpleXLSXEx;
use App\Transformers\ExcelParser;
use PHPUnit\Framework\TestCase;
use Shuchkin\SimpleXLSX;

class TestParseRoster extends TestCase
{
    public function _testParse() {

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
    public function testParseSolved() {

        // bandom instance padaryti FixedSimpleXLSXEx

//        $fixedEx = new FixedSimpleXLSXEx(new FixedSimpleXLSX());
        $excelFile = __DIR__."/data/solved.xlsx";
        $this->assertTrue( file_exists( $excelFile) );

        $xlsx = FixedSimpleXLSX::parse($excelFile);

        // check the red square and the time square
        $redCell = $xlsx->getCell(0, 'F10');
        $whiteCell = $xlsx->getCell(0, 'G14');
        $numberCell = $xlsx->getCell(0, 'G24');

        $rows = $xlsx->rowsEx(0);

        $this->assertEquals( '1970-01-01 00:00:09', $numberCell);


        $this->assertTrue(true);
    }

}