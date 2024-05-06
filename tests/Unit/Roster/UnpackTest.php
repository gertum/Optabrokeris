<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Employee;
use App\Domain\Roster\Schedule;
use PHPUnit\Framework\TestCase;

//use Tests\TestCase;

class UnpackTest extends TestCase
{
    /**
     * @dataProvider provideScheduleData
     */
    public function testUnpack(string $data, Schedule $expectedSchedule)
    {
        $decodedData = json_decode($data, true);
        $schedule = new Schedule($decodedData);

        $this->assertEquals($expectedSchedule, $schedule);
    }

    public static function provideScheduleData(): array
    {
        return [

            'most simple' => [
                'data' => '{
                 "employeeList": [
                    {
                      "name": "Amy King",
                      "skillSet": [
                        "Cardiology",
                        "Anaesthetics",
                        "Nurse"
                      ]
                    }
                  ]
                }',
                'expectedSchedule' => (new Schedule())->setEmployeeList([
                    (new Employee())
                        ->setName("Amy King")
                        ->setSkillSet([
                            "Cardiology",
                            "Anaesthetics",
                            "Nurse"
                        ])
                ])
            ],
//            'simple' => [
//                'data' => '{
//  "availabilityList": [
//    {
//      "id": 1,
//      "employee": {
//        "name": "Hugo King",
//        "skillSet": [
//          "Anaesthetics",
//          "Nurse"
//        ]
//      },
//      "date": "2024-04-08",
//      "availabilityType": "DESIRED"
//    }
//  ],
//  "employeeList": [
//    {
//      "name": "Amy King",
//      "skillSet": [
//        "Cardiology",
//        "Anaesthetics",
//        "Nurse"
//      ]
//    }
//  ],
//  "shiftList": [
//    {
//      "id": 4,
//      "start": "2024-04-08T06:00:00",
//      "end": "2024-04-08T14:00:00",
//      "location": "Ambulatory care",
//      "requiredSkill": "Anaesthetics",
//      "employee": null
//    }
//  ],
//
//  "score": "-133init/0hard/0soft",
//  "scheduleState": {
//    "tenantId": 1,
//    "publishLength": 7,
//    "draftLength": 14,
//    "firstDraftDate": "2024-04-08",
//    "lastHistoricDate": "2024-04-01",
//    "firstUnplannedDate": "2024-04-22"
//  },
//  "solverStatus": "NOT_SOLVING"
//}',
//                'expectedSchedule' =>
//                    (new Schedule())
//                        ->setEmployeeList(
//                            [
//                                (new Employee())
//                                    ->setName("Hugo King")
//                                    ->setSkillSet([
//                                        "Anaesthetics",
//                                        "Nurse"
//                                    ])
//                                    ->setDate("2024-04-08")
//                                    ->setAvailabilityType("DESIRED")
//                            ]
//                        )
//                        ->setAvailabilityList([
//                            (new Availability())
//                        ])
//            ]
        ];
    }

}