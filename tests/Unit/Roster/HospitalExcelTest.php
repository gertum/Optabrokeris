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
        $expectedEilNrs = $this->getExpectedEilNrs();
        $eilNrs = $wrapper->parseEilNrs($eilNrTitle);
        $this->assertEquals($expectedEilNrs, $eilNrs);


        $expectedEmployees = $this->getExpectedEmployees();
        $employees = $wrapper->parseEmployees($eilNrs);

        // not wise to compare whole objects
//        $this->assertEquals($expectedEmployees, $employees);
        $expectedEmployeesNames = array_map(fn(Employee $e) => $e->name, $expectedEmployees);
        $employeesNames = array_map(fn(Employee $e) => $e->name, $employees);

        $this->assertEquals($expectedEmployeesNames, $employeesNames);

        $groupedExpectedAvailabilities = $this->getGroupedExpectedAvailabilities();
        $expectedAvailabilities1 = $wrapper->parseAvailabilitiesForEilNr($eilNrs[0], 2024, 2, $employees[0]);

        $this->assertEquals($groupedExpectedAvailabilities[1], $expectedAvailabilities1);

        $availabilities = $wrapper->parseAvailabilities($eilNrs, $employees, 2024, 2);

        $this->assertEquals($groupedExpectedAvailabilities, $availabilities);
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


    public function getExpectedEilNrs(): array
    {
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
    public function getGroupedExpectedAvailabilities(): array
    {
        $employees = $this->getExpectedEmployees();
        return [
            1 => [
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(2)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(3)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(4)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(5)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(6)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(7)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(8)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(9)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(10)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(11)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(12)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(13)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(14)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(15)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(16)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(17)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(18)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(19)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(20)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(21)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(22)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(23)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(24)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(25)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(26)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(27)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(28)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[0]),
                (new Availability())
                    ->setId(29)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[0]),
            ],
            2 => [
                (new Availability())
                    ->setId(30)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(31)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(32)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(33)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(34)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(35)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(36)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(37)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(38)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(39)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(40)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(41)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(42)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(43)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(44)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(44)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(45)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(46)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(47)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(48)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(49)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(50)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(51)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(52)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(53)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(54)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(55)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(56)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(57)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[1]),
            ],
            3 => [
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
            ],
            4 => [
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3])
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
            ],
            5 => [
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
            ],
            6 => [
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
            ],
            7 => [
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
            ],
            8 => [
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
            ],
            9 => [
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
            ],
            10 => [
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
            ],
            11 => [
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[10]),
            ],
            12 => [
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::UNDESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
            ],
            13 => [
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
            ],
            14 => [
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[13]),
            ],
            15 => [
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
            ],
            16 => [
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
            ],
            17 => [
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[16]),
            ],
            18 => [
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
            ],
            19 => [
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
            ],
            20 => [
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
            ],
            21 => [
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20])
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(1)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
            ],
        ];
    }


}

