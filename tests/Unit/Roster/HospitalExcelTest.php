<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Employee;
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

        // TODO availabilities
        // TODO employees

        $employeesNames = [
            'Renata Juknevičienė 29/12',
            'Aleksandras Briedis 24/12',
            'Julius Jaramavičius 42/12',
            'Paulius Uksas 38',
            'Iveta Vėgelytė 41/24',
            'Raminta Konciene 70/36',
            'Giedrius Montrimas 67',
            'Lina Šimėnaitė 37/24',
            'Grakauskienė 89/72',
            'Laura Zajančkovskytė 129/84',
            'Tomas Trybė 87/48',
            'Vesta Aleliūnienė 137/84',
            'Karolis Skaisgirys 37',
            'Eglė Politikaitė 40/24',
            'Edgaras Baliūnas 18 val.',
            'Samanta Plikaitytė 40',
            'Dovilė Petrušytė 24',
            'Narvoiš 40',
            'serbentaite',
            'Michail Lapida 40',
            'Rinkūnas',
            'Valerija',
            'Raminta',
            'Jonas',
            'Beatričė',
        ];

        $expectedEmployees = array_map(
            fn($name) => (new Employee())->setName($name)
            , $employeesNames
        );

        $this->assertEquals( $expectedEmployees, $wrapper->getEmployees());

        
        // TODO shifts
    }
}

