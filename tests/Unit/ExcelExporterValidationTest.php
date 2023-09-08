<?php

namespace Tests\Unit;

use App\Exceptions\SolverDataException;
use App\Transformers\ExcelSchoolDataHandler;
use PHPUnit\Framework\TestCase;

class ExcelExporterValidationTest extends TestCase
{

    public function testMissingTimeslotList()
    {
        $h = new ExcelSchoolDataHandler();

        $this->expectException(SolverDataException::class);
        $this->expectExceptionMessageMatches('#timeslotList#');
        $data = [];

        $tmpName = tempnam('/tmp', 'test');
        $h->arrayToSpreadSheet($data, $tmpName);
        // should not reach here
    }

    public function testMissingRoomList()
    {
        $h = new ExcelSchoolDataHandler();

        $this->expectException(SolverDataException::class);
        $this->expectExceptionMessageMatches('#roomList#');
        $data = ['timeslotList' => []];

        $tmpName = tempnam('/tmp', 'test');
        $h->arrayToSpreadSheet($data, $tmpName);
        // should not reach here
    }

    public function testMissingLessonList()
    {
        $h = new ExcelSchoolDataHandler();

        $this->expectException(SolverDataException::class);
        $this->expectExceptionMessageMatches('#lessonList#');
        $data = [
            'timeslotList' => [],
            'roomList' => [],
        ];

        $tmpName = tempnam('/tmp', 'test');
        $h->arrayToSpreadSheet($data, $tmpName);
        // should not reach here
    }

    public function testLessonKeys()
    {
        $h = new ExcelSchoolDataHandler();

        $this->expectException(SolverDataException::class);
        $this->expectExceptionMessageMatches('#room#');
        $data = [
            'timeslotList' => [],
            'roomList' => [],
            'lessonList' => [
                ['aaa'],
                ['bbb'],
            ],
        ];

        $tmpName = tempnam('/tmp', 'test');
        $h->arrayToSpreadSheet($data, $tmpName);
        // should not reach here
    }

    public function testLessonKeys2()
    {
        $h = new ExcelSchoolDataHandler();

        $this->expectException(SolverDataException::class);
        $this->expectExceptionMessageMatches('#timeslot#');
        $data = [
            'timeslotList' => [],
            'roomList' => [],
            'lessonList' => [
                ['room' => ['id' => 'abc'], 'timeslot2' => 'def'],
            ],
        ];

        $tmpName = tempnam('/tmp', 'test');
        $h->arrayToSpreadSheet($data, $tmpName);
        // should not reach here
    }

    public function testLessonKeysOk()
    {
        $h = new ExcelSchoolDataHandler();

//        $this->expectException(SolverDataException::class);
//        $this->expectExceptionMessageMatches('#timeslot#');
        $data = [
            'timeslotList' => [],
            'roomList' => [],
            'lessonList' => [
                ['room' => ['id' => 'abc'], 'timeslot' => ['id' => 'def']],
            ],
        ];

        $tmpName = tempnam('/tmp', 'test');
        $h->arrayToSpreadSheet($data, $tmpName);
        // should not reach here
        $this->assertTrue(true);
    }

    public function testMissingId()
    {
        $h = new ExcelSchoolDataHandler();

        $this->expectException(SolverDataException::class);
        $this->expectExceptionMessageMatches('#id#');
        $data = [
            'timeslotList' => [],
            'roomList' => [],
            'lessonList' => [
                ['room' => ['id' => 'abc'], 'timeslot' => ['def']],
            ],
        ];

        $tmpName = tempnam('/tmp', 'test');
        $h->arrayToSpreadSheet($data, $tmpName);
        // should not reach here
        $this->assertTrue(true);
    }
}
