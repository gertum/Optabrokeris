<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Employee;
use App\Domain\Roster\Hospital\EilNr;
use App\Domain\Roster\Hospital\EilNrTitle;
use App\Domain\Roster\Hospital\ExcelWrapper;
use PHPUnit\Framework\TestCase;

class HospitalExcelTest extends TestCase
{
    public function testParse()
    {
        $excelFile = __DIR__ . "/data/vasaris.xlsx";
        $wrapper = ExcelWrapper::parse($excelFile);

        $cellF12 = $wrapper->getCell(11, 5);
        $cellG12 = $wrapper->getCell(11, 6);

        $this->assertEquals('F12', $cellF12->name);
        $this->assertEquals('G12', $cellG12->name);

        $this->assertEquals(
            'color: #000000;font-family: Arial1;font-size: 13px;background-color: #FFFFFF;text-align: center;vertical-align: middle;border-top-style: solid;border-top-color: #000000;border-top-width: thin;border-right-style: solid;border-right-color: #;border-right-width: thin;border-bottom-style: solid;border-bottom-color: #;border-bottom-width: thin;border-left-style: solid;border-left-color: #000000;border-left-width: thin;',
            $cellF12->css
        );
        $this->assertEquals(
            'color: #000000;font-family: Arial1;font-size: 13px;background-color: #FF0000;text-align: center;vertical-align: middle;border-top-style: solid;border-top-color: #000000;border-top-width: thin;border-right-style: solid;border-right-color: #000000;border-right-width: thin;border-bottom-style: solid;border-bottom-color: #000000;border-bottom-width: thin;border-left-style: solid;border-left-color: #000000;border-left-width: thin;',
            $cellG12->css
        );


        $this->assertEquals([
            'color' => '#000000',
            'font-family' => 'Arial1',
            'font-size' => '13px',
            'background-color' => '#FF0000',
            'text-align' => 'center',
            'vertical-align' => 'middle',
            'border-top-style' => 'solid',
            'border-top-color' => '#000000',
            'border-top-width' => 'thin',
            'border-right-style' => 'solid',
            'border-right-color' => '#000000',
            'border-right-width' => 'thin',
            'border-bottom-style' => 'solid',
            'border-bottom-color' => '#000000',
            'border-bottom-width' => 'thin',
            'border-left-style' => 'solid',
            'border-left-color' => '#000000',
            'border-left-width' => 'thin',
        ], $cellG12->getParsedCss());

        $this->assertEquals('#FF0000', $cellG12->getBackgroundColor());


        // 1) top left corner
        $expectedEilNrTitle = (new EilNrTitle())->setRow(7)->setColumn(0);
        $eilNrTitle = $wrapper->findEilNrTitle();
        $this->assertEquals($expectedEilNrTitle, $eilNrTitle);


        // 2) the first column
        $expectedEilNrs  = $this->getExpectedEilNrs();
        $eilNrs = $wrapper->parseEilNrs($eilNrTitle);
        $this->assertEquals($expectedEilNrs, $eilNrs);


        $expectedEmployees = $this->getExpectedEmployees();
        $employees = $wrapper->parseEmployees($eilNrs);

        $this->assertEquals($expectedEmployees, $employees);

        // TODO availabilities
        // TODO shifts
    }


    /**
     * @return Employee[]
     */
    public function getExpectedEmployees(): array
    {
        return [
            (new Employee())
                ->setName('Renata Juknevičienė 29/12')
                ->setExcelRow(10),
            (new Employee())
                ->setName('Aleksandras Briedis 24/12')
                ->setExcelRow(12),
            (new Employee())
                ->setName('Julius Jaramavičius 42/12')
                ->setExcelRow(14),
            (new Employee())
                ->setName('Paulius Uksas 38')
                ->setExcelRow(16),
            (new Employee())
                ->setName('Iveta Vėgelytė 41/24')
                ->setExcelRow(18),
            (new Employee())
                ->setName('Raminta Konciene 70/36')
                ->setExcelRow(20),
            (new Employee())
                ->setName('Giedrius Montrimas 67')
                ->setExcelRow(22),
            (new Employee())
                ->setName('Lina Šimėnaitė 37/24')
                ->setExcelRow(24),
            (new Employee())
                ->setName('Grakauskienė 89/72')
                ->setExcelRow(26),
            (new Employee())
                ->setName('Laura Zajančkovskytė 129/84')
                ->setExcelRow(28),
            (new Employee())
                ->setName('Tomas Trybė 87/48')
                ->setExcelRow(30),
            (new Employee())
                ->setName('Vesta Aleliūnienė 137/84')
                ->setExcelRow(32),
            (new Employee())
                ->setName('Karolis Skaisgirys 37')
                ->setExcelRow(34),
            (new Employee())
                ->setName('Eglė Politikaitė 40/24')
                ->setExcelRow(36),
            (new Employee())
                ->setName('Edgaras Baliūnas 18 val.')
                ->setExcelRow(38),
            (new Employee())
                ->setName('Samanta Plikaitytė 40')
                ->setExcelRow(40),
            (new Employee())
                ->setName('Dovilė Petrušytė 24')
                ->setExcelRow(42),
            (new Employee())
                ->setName('Narvoiš 40')
                ->setExcelRow(44),
            (new Employee())
                ->setName('serbentaite')
                ->setExcelRow(46),
            (new Employee())
                ->setName('Michail Lapida 40')
                ->setExcelRow(48),
            (new Employee())
                ->setName('Rinkūnas')
                ->setExcelRow(50),
        ];
    }


    /**
     * @return Availability[][]
     */
    public function getGroupedExpectedAvailabilities() : array {
        return [
            ''
        ];
    }

    public function getExpectedEilNrs() : array {
        return [
            (new EilNr())->setValue(1)->setRow(9)->setColumn(0),
            (new EilNr())->setValue(2)->setRow(11)->setColumn(0),
            (new EilNr())->setValue(3)->setRow(13)->setColumn(0),
            (new EilNr())->setValue(4)->setRow(15)->setColumn(0),
            (new EilNr())->setValue(5)->setRow(17)->setColumn(0),
            (new EilNr())->setValue(6)->setRow(19)->setColumn(0),
            (new EilNr())->setValue(7)->setRow(21)->setColumn(0),
            (new EilNr())->setValue(8)->setRow(23)->setColumn(0),
            (new EilNr())->setValue(9)->setRow(25)->setColumn(0),
            (new EilNr())->setValue(10)->setRow(27)->setColumn(0),
            (new EilNr())->setValue(11)->setRow(29)->setColumn(0),
            (new EilNr())->setValue(12)->setRow(31)->setColumn(0),
            (new EilNr())->setValue(13)->setRow(33)->setColumn(0),
            (new EilNr())->setValue(14)->setRow(35)->setColumn(0),
            (new EilNr())->setValue(15)->setRow(37)->setColumn(0),
            (new EilNr())->setValue(16)->setRow(39)->setColumn(0),
            (new EilNr())->setValue(17)->setRow(41)->setColumn(0),
            (new EilNr())->setValue(18)->setRow(43)->setColumn(0),
            (new EilNr())->setValue(19)->setRow(45)->setColumn(0),
            (new EilNr())->setValue(20)->setRow(47)->setColumn(0),
            (new EilNr())->setValue(21)->setRow(49)->setColumn(0),
        ];
    }
}

