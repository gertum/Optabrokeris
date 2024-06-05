<?php

namespace Draft;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Employee;
use App\Domain\Roster\Hospital\EilNr;
use App\Domain\Roster\Hospital\EilNrTitle;
use App\Domain\Roster\Hospital\ExcelWrapper;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;


/**
 * Semi draft test TOO heavy
 * Will check only part of results in the future.
 */
class HospitalExcelAvailabilitiesTest extends TestCase
{
    public function _testParse()
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

        // not wise to compare whole objects ?
//        $this->assertEquals($expectedEmployees, $employees);
        $expectedEmployeesNames = array_map(fn(Employee $e) => $e->name, $expectedEmployees);
        $employeesNames = array_map(fn(Employee $e) => $e->name, $employees);

        $this->assertEquals($expectedEmployeesNames, $employeesNames);

        $groupedExpectedAvailabilities = $this->getGroupedExpectedAvailabilities();
//        $expectedAvailabilities1 = $wrapper->parseAvailabilitiesForEilNr($eilNrs[0], 2024, 2, $employees[0]);

//        $this->assertEquals($groupedExpectedAvailabilities[1], $expectedAvailabilities1);

        $availabilities = $wrapper->parseAvailabilities($eilNrs, $employees, 2024, 2);

        $this->assertEquals($groupedExpectedAvailabilities, $availabilities);

//         $this->assertEquals( $groupedExpectedAvailabilities[1], $availabilities[1] );
//         $this->assertEquals( $groupedExpectedAvailabilities[2], $availabilities[2] );
//         $this->assertEquals( $groupedExpectedAvailabilities[3], $availabilities[3] );
//         $this->assertEquals( $groupedExpectedAvailabilities[21], $availabilities[21] );
        // TODO shifts
    }


    /**
     * @return Employee[]
     */
    public function getExpectedEmployees(): array
    {
        return [
            (new Employee())
                ->setSequenceNumber(1)
                ->setName('Renata Juknevičienė 29/12')
                ->setExcelRow(10)
                ->setRow(9)
            ,
            (new Employee())
                ->setSequenceNumber(2)
                ->setName('Aleksandras Briedis 24/12')
                ->setExcelRow(12)
                ->setRow(11)
            ,
            (new Employee())
                ->setSequenceNumber(3)
                ->setName('Julius Jaramavičius 42/12')
                ->setExcelRow(14)
                ->setRow(13)
            ,
            (new Employee())
                ->setSequenceNumber(4)
                ->setName('Paulius Uksas 38')
                ->setExcelRow(16)
                ->setRow(15)
            ,
            (new Employee())
                ->setSequenceNumber(5)
                ->setName('Iveta Vėgelytė 41/24')
                ->setExcelRow(18)
                ->setRow(17)
            ,
            (new Employee())
                ->setSequenceNumber(6)
                ->setName('Raminta Konciene 70/36')
                ->setExcelRow(20)
                ->setRow(19)
            ,
            (new Employee())
                ->setSequenceNumber(7)
                ->setName('Giedrius Montrimas 67')
                ->setExcelRow(22)
                ->setRow(21)
            ,
            (new Employee())
                ->setSequenceNumber(8)
                ->setName('Lina Šimėnaitė 37/24')
                ->setExcelRow(24)
                ->setRow(23)
            ,
            (new Employee())
                ->setSequenceNumber(9)
                ->setName('Grakauskienė 89/72')
                ->setExcelRow(26)
                ->setRow(25)
            ,
            (new Employee())
                ->setSequenceNumber(10)
                ->setName('Laura Zajančkovskytė 129/84')
                ->setExcelRow(28)
                ->setRow(27)
            ,
            (new Employee())
                ->setSequenceNumber(11)
                ->setName('Tomas Trybė 87/48')
                ->setExcelRow(30)
                ->setRow(29)
            ,
            (new Employee())
                ->setSequenceNumber(12)
                ->setName('Vesta Aleliūnienė 137/84')
                ->setExcelRow(32)
                ->setRow(31)
            ,
            (new Employee())
                ->setSequenceNumber(13)
                ->setName('Karolis Skaisgirys 37')
                ->setExcelRow(34)
                ->setRow(33)
            ,
            (new Employee())
                ->setSequenceNumber(14)
                ->setName('Eglė Politikaitė 40/24')
                ->setExcelRow(36)
                ->setRow(35)
            ,
            (new Employee())
                ->setSequenceNumber(15)
                ->setName('Edgaras Baliūnas 18 val.')
                ->setExcelRow(38)
                ->setRow(37)
            ,
            (new Employee())
                ->setSequenceNumber(16)
                ->setName('Samanta Plikaitytė 40')
                ->setExcelRow(40)
                ->setRow(39)
            ,
            (new Employee())
                ->setSequenceNumber(17)
                ->setName('Dovilė Petrušytė 24')
                ->setExcelRow(42)
                ->setRow(41)
            ,
            (new Employee())
                ->setSequenceNumber(18)
                ->setName('Narvoiš 40')
                ->setExcelRow(44)
                ->setRow(43)
            ,
            (new Employee())
                ->setSequenceNumber(19)
                ->setName('serbentaite')
                ->setExcelRow(46)
                ->setRow(45)
            ,
            (new Employee())
                ->setSequenceNumber(20)
                ->setName('Michail Lapida 40')
                ->setExcelRow(48)
                ->setRow(47)
            ,
            (new Employee())
                ->setSequenceNumber(21)
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
                    ->setId(45)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(46)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(47)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(48)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(49)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(50)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(51)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(52)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(53)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(54)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(55)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(56)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(57)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[1]),
                (new Availability())
                    ->setId(58)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[1]),
            ],
            3 => [
                (new Availability())
                    ->setId(59)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(60)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(61)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(62)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(63)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(64)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(65)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(66)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(67)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(68)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(69)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(70)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(71)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(72)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(73)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(74)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(75)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(76)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(77)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(78)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(79)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(80)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(81)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(82)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(83)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(84)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(85)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(86)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
                (new Availability())
                    ->setId(87)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[2]),
            ],
            4 => [
                (new Availability())
                    ->setId(88)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3])
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(89)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(90)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(91)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(92)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(93)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(94)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(95)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(96)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(97)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(98)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(99)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(100)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(101)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(102)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(103)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(104)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(105)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(106)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(107)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(108)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(109)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(110)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(111)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(112)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(113)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(114)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(115)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
                (new Availability())
                    ->setId(116)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[3]),
            ],
            5 => [
                (new Availability())
                    ->setId(117)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(118)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(119)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(120)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(121)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(122)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(123)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(124)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(125)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(126)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(127)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(128)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(129)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(130)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(131)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(132)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(133)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(134)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(135)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(136)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(137)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(138)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(139)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(140)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(141)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(142)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(143)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(144)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
                (new Availability())
                    ->setId(145)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[4]),
            ],
            6 => [
                (new Availability())
                    ->setId(146)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(147)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(148)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(149)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(150)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(151)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(152)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(153)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(154)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(155)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(156)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(157)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(158)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(159)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(160)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(161)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(162)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(163)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(164)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(165)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(166)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(167)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(168)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(169)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(170)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(171)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(172)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(173)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[5]),
                (new Availability())
                    ->setId(174)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[5]),
            ],
            7 => [
                (new Availability())
                    ->setId(175)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(176)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(177)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(178)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(179)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(180)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(181)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(182)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(183)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(184)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(185)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(186)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(187)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(188)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(189)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(190)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(191)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(192)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(193)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(194)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(195)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(196)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(197)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(198)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(199)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(200)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(201)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(202)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
                (new Availability())
                    ->setId(203)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[6]),
            ],
            8 => [
                (new Availability())
                    ->setId(204)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(205)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(206)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(207)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(208)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(209)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(210)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(211)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(212)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(213)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(214)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(215)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(216)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(217)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(218)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(219)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(220)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(221)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(222)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(223)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(224)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(225)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(226)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(227)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(228)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(229)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(230)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(231)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
                (new Availability())
                    ->setId(232)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[7]),
            ],
            9 => [
                (new Availability())
                    ->setId(233)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(234)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(235)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(236)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(237)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(238)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(239)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(240)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(241)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(242)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(243)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(244)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(245)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(246)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(247)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(248)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(249)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(250)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(251)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(252)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(253)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(254)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(255)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(256)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(257)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(258)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(259)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(260)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
                (new Availability())
                    ->setId(261)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[8]),
            ],
            10 => [
                (new Availability())
                    ->setId(262)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(263)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(264)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(265)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(266)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(267)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(268)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(269)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(270)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(271)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(272)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(273)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(274)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(275)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(276)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(277)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(278)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(279)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(280)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(281)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(282)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(283)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(284)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(285)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(286)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(287)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(288)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(289)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
                (new Availability())
                    ->setId(290)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[9]),
            ],
            11 => [
                (new Availability())
                    ->setId(291)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(292)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(293)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(294)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(295)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(296)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(297)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(298)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(299)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(300)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(301)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(302)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(303)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(304)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(305)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(306)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(307)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(308)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(309)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(310)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(311)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(312)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(313)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(314)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(315)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(316)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(317)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(318)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[10]),
                (new Availability())
                    ->setId(319)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[10]),
            ],
            12 => [
                (new Availability())
                    ->setId(320)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(321)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(322)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(323)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(324)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(325)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(326)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(327)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(328)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(329)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(330)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(331)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(332)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(333)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(334)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(335)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(336)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(337)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(338)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(339)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(340)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(341)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(342)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(343)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(344)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(345)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(346)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(347)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
                (new Availability())
                    ->setId(348)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[11]),
            ],
            13 => [
                (new Availability())
                    ->setId(349)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(350)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(351)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(352)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(353)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(354)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(355)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(356)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(357)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(358)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(359)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(360)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(361)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(362)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(363)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(364)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(365)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(366)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(367)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(368)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(369)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(370)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(371)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(372)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(373)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(374)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(375)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(376)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
                (new Availability())
                    ->setId(377)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[12]),
            ],
            14 => [
                (new Availability())
                    ->setId(378)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(379)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(380)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(381)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(382)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(383)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(384)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(385)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(386)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(387)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(388)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(389)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(390)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(391)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(392)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(393)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(394)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(395)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(396)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(397)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(398)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(399)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(400)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(401)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(402)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(403)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(404)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(405)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[13]),
                (new Availability())
                    ->setId(406)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[13]),
            ],
            15 => [
                (new Availability())
                    ->setId(407)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(408)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(409)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(410)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(411)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(412)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(413)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(414)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(415)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(416)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(417)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(418)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(419)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(420)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(421)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(422)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(423)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(424)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(425)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(426)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(427)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(428)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(429)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(430)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(431)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(432)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(433)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(434)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
                (new Availability())
                    ->setId(435)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[14]),
            ],
            16 => [
                (new Availability())
                    ->setId(436)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(437)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(438)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(439)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(440)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(441)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(442)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(443)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(444)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(445)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(446)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(447)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(448)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(449)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(450)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(451)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(452)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(453)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(454)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(455)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(456)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(457)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(458)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(459)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(460)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(461)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(462)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(463)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
                (new Availability())
                    ->setId(464)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[15]),
            ],
            17 => [
                (new Availability())
                    ->setId(465)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(466)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(467)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(468)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(469)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(470)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(471)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(472)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(473)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(474)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(475)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(476)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(477)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(478)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(479)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(480)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(481)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(482)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(483)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(484)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(485)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(486)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(487)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(488)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(489)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(490)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(491)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(492)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[16]),
                (new Availability())
                    ->setId(493)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[16]),
            ],
            18 => [
                (new Availability())
                    ->setId(494)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(495)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(496)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(497)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(498)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(499)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(500)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(501)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(502)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(503)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(504)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(505)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(506)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(507)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(508)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(509)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(510)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(511)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(512)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(513)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(514)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(515)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(516)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(517)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(518)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(519)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(520)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(521)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
                (new Availability())
                    ->setId(522)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[17]),
            ],
            19 => [
                (new Availability())
                    ->setId(523)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(524)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(525)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(526)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(527)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(528)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(529)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(530)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(531)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(532)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(533)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(534)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(535)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(536)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(537)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(538)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(539)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(540)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(541)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(542)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(543)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(544)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(545)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(546)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(547)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(548)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(549)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(550)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
                (new Availability())
                    ->setId(551)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[18]),
            ],
            20 => [
                (new Availability())
                    ->setId(552)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(553)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(554)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(555)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(556)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(557)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(558)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(559)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(560)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(561)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(562)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(563)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(564)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(565)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(566)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(567)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(568)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(569)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(570)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(571)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(572)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(573)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(574)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(575)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(576)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(577)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(578)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(579)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
                (new Availability())
                    ->setId(580)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[19]),
            ],
            21 => [
                (new Availability())
                    ->setId(581)
                    ->setDate(Carbon::parse('2024-02-01'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20])
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(582)
                    ->setDate(Carbon::parse('2024-02-02'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(583)
                    ->setDate(Carbon::parse('2024-02-03'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(584)
                    ->setDate(Carbon::parse('2024-02-04'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(585)
                    ->setDate(Carbon::parse('2024-02-05'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(586)
                    ->setDate(Carbon::parse('2024-02-06'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(587)
                    ->setDate(Carbon::parse('2024-02-07'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(588)
                    ->setDate(Carbon::parse('2024-02-08'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(589)
                    ->setDate(Carbon::parse('2024-02-09'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(590)
                    ->setDate(Carbon::parse('2024-02-10'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(591)
                    ->setDate(Carbon::parse('2024-02-11'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(592)
                    ->setDate(Carbon::parse('2024-02-12'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(593)
                    ->setDate(Carbon::parse('2024-02-13'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(594)
                    ->setDate(Carbon::parse('2024-02-14'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(595)
                    ->setDate(Carbon::parse('2024-02-15'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(596)
                    ->setDate(Carbon::parse('2024-02-16'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(597)
                    ->setDate(Carbon::parse('2024-02-17'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(598)
                    ->setDate(Carbon::parse('2024-02-18'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(599)
                    ->setDate(Carbon::parse('2024-02-19'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(600)
                    ->setDate(Carbon::parse('2024-02-20'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(601)
                    ->setDate(Carbon::parse('2024-02-21'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(602)
                    ->setDate(Carbon::parse('2024-02-22'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(603)
                    ->setDate(Carbon::parse('2024-02-23'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(604)
                    ->setDate(Carbon::parse('2024-02-24'))
                    ->setAvailabilityType(Availability::UNAVAILABLE)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(605)
                    ->setDate(Carbon::parse('2024-02-25'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(606)
                    ->setDate(Carbon::parse('2024-02-26'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(607)
                    ->setDate(Carbon::parse('2024-02-27'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(608)
                    ->setDate(Carbon::parse('2024-02-28'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
                (new Availability())
                    ->setId(609)
                    ->setDate(Carbon::parse('2024-02-29'))
                    ->setAvailabilityType(Availability::DESIRED)
                    ->setEmployee($employees[20]),
            ],
        ];
    }


}

