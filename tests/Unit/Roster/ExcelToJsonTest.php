<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Hospital\ScheduleParser;
use PHPUnit\Framework\TestCase;

/**
 * Not very good test, because it compares exact result,
 * which may not work in future after various changes, that is still in the business logic.
 * But this test displays the json example for the further development.
 */
class ExcelToJsonTest extends TestCase
{

    /**
     * @dataProvider provideXlsxAndJson
     */
    public function testReadFilled(string $xslxFile, string $expectedJson)
    {
        $scheduleParser = new ScheduleParser();
        $schedule = $scheduleParser->parseScheduleXls($xslxFile, ScheduleParser::createHospitalTimeSlices());
        $json = json_encode($schedule->toArray());

        $this->assertEquals($expectedJson, $json);
    }

    public static function provideXlsxAndJson(): array
    {
        return [
            'test1' => [
                'xlsxFile' => __DIR__ . '/data/small_partly_filled.xlsx',
                'expectedJson' => '{"availabilityList":[{"id":1,"employee":{"name":"Renata Juknevi\u010dien\u0117 29\/12","skillSet":null,"maxWorkingHours":74},"date":"2024-06-01T00:00:00.000000Z","availabilityType":"UNAVAILABLE"},{"id":2,"employee":{"name":"Renata Juknevi\u010dien\u0117 29\/12","skillSet":null,"maxWorkingHours":74},"date":"2024-06-02T00:00:00.000000Z","availabilityType":"UNAVAILABLE"},{"id":3,"employee":{"name":"Renata Juknevi\u010dien\u0117 29\/12","skillSet":null,"maxWorkingHours":74},"date":"2024-06-03T00:00:00.000000Z","availabilityType":"DESIRED"},{"id":4,"employee":{"name":"Renata Juknevi\u010dien\u0117 29\/12","skillSet":null,"maxWorkingHours":74},"date":"2024-06-04T00:00:00.000000Z","availabilityType":"DESIRED"},{"id":5,"employee":{"name":"Renata Juknevi\u010dien\u0117 29\/12","skillSet":null,"maxWorkingHours":74},"date":"2024-06-05T00:00:00.000000Z","availabilityType":"UNAVAILABLE"},{"id":6,"employee":{"name":"Renata Juknevi\u010dien\u0117 29\/12","skillSet":null,"maxWorkingHours":74},"date":"2024-06-06T00:00:00.000000Z","availabilityType":"DESIRED"},{"id":7,"employee":{"name":"Aleksandras Briedis 24\/12","skillSet":null,"maxWorkingHours":74},"date":"2024-06-01T00:00:00.000000Z","availabilityType":"DESIRED"},{"id":8,"employee":{"name":"Aleksandras Briedis 24\/12","skillSet":null,"maxWorkingHours":74},"date":"2024-06-02T00:00:00.000000Z","availabilityType":"DESIRED"},{"id":9,"employee":{"name":"Aleksandras Briedis 24\/12","skillSet":null,"maxWorkingHours":74},"date":"2024-06-03T00:00:00.000000Z","availabilityType":"UNAVAILABLE"},{"id":10,"employee":{"name":"Aleksandras Briedis 24\/12","skillSet":null,"maxWorkingHours":74},"date":"2024-06-04T00:00:00.000000Z","availabilityType":"UNAVAILABLE"},{"id":11,"employee":{"name":"Aleksandras Briedis 24\/12","skillSet":null,"maxWorkingHours":74},"date":"2024-06-05T00:00:00.000000Z","availabilityType":"DESIRED"},{"id":12,"employee":{"name":"Aleksandras Briedis 24\/12","skillSet":null,"maxWorkingHours":74},"date":"2024-06-06T00:00:00.000000Z","availabilityType":"UNAVAILABLE"}],"employeeList":[{"name":"Renata Juknevi\u010dien\u0117 29\/12","skillSet":null,"maxWorkingHours":74},{"name":"Aleksandras Briedis 24\/12","skillSet":null,"maxWorkingHours":74}],"shiftList":[{"id":1,"start":"2024-06-01T00:00:00","end":"2024-06-01T08:00:00","location":null,"requiredSkill":null,"employee":null},{"id":2,"start":"2024-06-01T08:00:00","end":"2024-06-01T20:00:00","location":null,"requiredSkill":null,"employee":null},{"id":3,"start":"2024-06-01T20:00:00","end":"2024-06-02T00:00:00","location":null,"requiredSkill":null,"employee":null},{"id":4,"start":"2024-06-02T00:00:00","end":"2024-06-02T08:00:00","location":null,"requiredSkill":null,"employee":null},{"id":5,"start":"2024-06-02T08:00:00","end":"2024-06-02T20:00:00","location":null,"requiredSkill":null,"employee":null},{"id":6,"start":"2024-06-02T20:00:00","end":"2024-06-03T00:00:00","location":null,"requiredSkill":null,"employee":{"name":"Aleksandras Briedis 24\/12","skillSet":null,"maxWorkingHours":74}},{"id":7,"start":"2024-06-03T00:00:00","end":"2024-06-03T08:00:00","location":null,"requiredSkill":null,"employee":null},{"id":8,"start":"2024-06-03T08:00:00","end":"2024-06-03T20:00:00","location":null,"requiredSkill":null,"employee":{"name":"Renata Juknevi\u010dien\u0117 29\/12","skillSet":null,"maxWorkingHours":74}},{"id":9,"start":"2024-06-03T20:00:00","end":"2024-06-04T00:00:00","location":null,"requiredSkill":null,"employee":null},{"id":10,"start":"2024-06-04T00:00:00","end":"2024-06-04T08:00:00","location":null,"requiredSkill":null,"employee":null},{"id":11,"start":"2024-06-04T08:00:00","end":"2024-06-04T20:00:00","location":null,"requiredSkill":null,"employee":null},{"id":12,"start":"2024-06-04T20:00:00","end":"2024-06-05T00:00:00","location":null,"requiredSkill":null,"employee":null},{"id":13,"start":"2024-06-05T00:00:00","end":"2024-06-05T08:00:00","location":null,"requiredSkill":null,"employee":null},{"id":14,"start":"2024-06-05T08:00:00","end":"2024-06-05T20:00:00","location":null,"requiredSkill":null,"employee":null},{"id":15,"start":"2024-06-05T20:00:00","end":"2024-06-06T00:00:00","location":null,"requiredSkill":null,"employee":null},{"id":16,"start":"2024-06-06T00:00:00","end":"2024-06-06T08:00:00","location":null,"requiredSkill":null,"employee":{"name":"Renata Juknevi\u010dien\u0117 29\/12","skillSet":null,"maxWorkingHours":74}},{"id":17,"start":"2024-06-06T08:00:00","end":"2024-06-06T20:00:00","location":null,"requiredSkill":null,"employee":null},{"id":18,"start":"2024-06-06T20:00:00","end":"2024-06-07T00:00:00","location":null,"requiredSkill":null,"employee":null}],"score":"-999999init\/0hard\/0soft","scheduleState":null,"solverState":null}'
            ]
        ];
    }
}