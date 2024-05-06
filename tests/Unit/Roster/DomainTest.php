<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Availability;
use App\Domain\Roster\Employee;
use App\Domain\Roster\Schedule;
use Illuminate\Support\Collection;
use Tests\TestCase;

class DomainTest extends TestCase
{
    public function testCreate() {

//         $employees = new Collection(
//            [
//                (new Employee())
//                    ->setName("Hugo King")
//                    ->setSkillSet([
//                        "Anaesthetics",
//                        "Nurse"
//                    ])
//                    ->setDate("2024-04-08")
//                    ->setAvailabilityType("DESIRED")
//            ]
//        );
//
//        $schedule = (new Schedule())
//            ->setEmployeeList(
//                new Collection(
//                    [
//                        (new Employee())
//                            ->setName("Hugo King")
//                            ->setSkillSet([
//                                "Anaesthetics",
//                                "Nurse"
//                            ])
//                            ->setDate("2024-04-08")
//                            ->setAvailabilityType("DESIRED")
//                    ]
//                )
//            )
//            ->setAvailabilityList([
//                (new Availability())
//            ]);
//
////        $schedule->setEmployeeList($employees);
//
//        $this->assertEquals(1, $schedule->employeeList->count() );

        $employee =  (new Employee())
            ->setName("Amy King")
            ->setSkillSet([
                "Cardiology",
                "Anaesthetics",
                "Nurse"
            ]);


        $employeeList = [$employee];

        $schedule = (new Schedule())->setEmployeeList($employeeList);

        $this->assertEquals(1,  count($schedule->employeeList));

    }

}