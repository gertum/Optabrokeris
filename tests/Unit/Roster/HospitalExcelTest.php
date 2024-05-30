<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Employee;
use App\Domain\Roster\Hospital\EilNr;
use App\Domain\Roster\Hospital\EilNrTitle;
use App\Domain\Roster\Hospital\ExcelWrapper;
use Carbon\Carbon;
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

        // not wise to compare whole objects
//        $this->assertEquals($expectedEmployees, $employees);
        $expectedEmployeesNames = array_map ( fn(Employee $e)=>$e->name, $expectedEmployees);
        $employeesNames = array_map ( fn(Employee $e)=>$e->name, $employees);

        $this->assertEquals($expectedEmployeesNames, $employeesNames);

        $groupedExpectedAvailabilities = $this->getGroupedExpectedAvailabilities();
        $expectedAvailabilities1 = $wrapper->parseAvailabilitiesForEilNr($eilNrs[0], 2024, 2 , $employees[0]);

        $this->assertEquals($groupedExpectedAvailabilities[1], $expectedAvailabilities1);

//        $availabilities = $wrapper->parseAvailabilities($eilNrs, $employees, 2024, 2);
//
//        $this->assertEquals($groupedExpectedAvailabilities, $availabilities);


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
                ->setExcelRow(10)
                ->setRow(9)
            ,
            (new Employee())
                ->setName('Aleksandras Briedis 24/12')
                ->setExcelRow(12)
                ->setRow(11)
            ,
            (new Employee())
                ->setName('Julius Jaramavičius 42/12')
                ->setExcelRow(14)
                ->setRow(13)
            ,
            (new Employee())
                ->setName('Paulius Uksas 38')
                ->setExcelRow(16)
                ->setRow(15)
            ,
            (new Employee())
                ->setName('Iveta Vėgelytė 41/24')
                ->setExcelRow(18)
                ->setRow(17)
            ,
            (new Employee())
                ->setName('Raminta Konciene 70/36')
                ->setExcelRow(20)
                ->setRow(19)
            ,
            (new Employee())
                ->setName('Giedrius Montrimas 67')
                ->setExcelRow(22)
                ->setRow(21)
            ,
            (new Employee())
                ->setName('Lina Šimėnaitė 37/24')
                ->setExcelRow(24)
                ->setRow(23)
            ,
            (new Employee())
                ->setName('Grakauskienė 89/72')
                ->setExcelRow(26)
                ->setRow(25)
            ,
            (new Employee())
                ->setName('Laura Zajančkovskytė 129/84')
                ->setExcelRow(28)
                ->setRow(27)
            ,
            (new Employee())
                ->setName('Tomas Trybė 87/48')
                ->setExcelRow(30)
                ->setRow(29)
            ,
            (new Employee())
                ->setName('Vesta Aleliūnienė 137/84')
                ->setExcelRow(32)
                ->setRow(31)
            ,
            (new Employee())
                ->setName('Karolis Skaisgirys 37')
                ->setExcelRow(34)
                ->setRow(33)
            ,
            (new Employee())
                ->setName('Eglė Politikaitė 40/24')
                ->setExcelRow(36)
                ->setRow(35)
            ,
            (new Employee())
                ->setName('Edgaras Baliūnas 18 val.')
                ->setExcelRow(38)
                ->setRow(37)
            ,
            (new Employee())
                ->setName('Samanta Plikaitytė 40')
                ->setExcelRow(40)
                ->setRow(39)
            ,
            (new Employee())
                ->setName('Dovilė Petrušytė 24')
                ->setExcelRow(42)
                ->setRow(41)
            ,
            (new Employee())
                ->setName('Narvoiš 40')
                ->setExcelRow(44)
                ->setRow(43)
            ,
            (new Employee())
                ->setName('serbentaite')
                ->setExcelRow(46)
                ->setRow(45)
            ,
            (new Employee())
                ->setName('Michail Lapida 40')
                ->setExcelRow(48)
                ->setRow(47)
            ,
            (new Employee())
                ->setName('Rinkūnas')
                ->setExcelRow(50)
                ->setRow(49)
            ,
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


    /**
     * @return Availability[][]
     */
    public function getGroupedExpectedAvailabilities() : array {
        $employees = $this->getExpectedEmployees();
        return [
            1 => [
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[0])
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                ->setEmployee($employees[0]),
                (new Availability())
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                ->setEmployee($employees[0]),
            ]
        ];
    }


}

