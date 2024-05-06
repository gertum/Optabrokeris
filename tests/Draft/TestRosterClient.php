<?php

namespace Draft;

use App\Solver\SolverClientFactory;
use App\Solver\SolverClientRoster;
use Tests\TestCase;

class TestRosterClient extends TestCase
{
    const MINIMAL = '{
  "availabilityList": [
    {
      "id": 1,
      "employee": {
        "name": "Hugo King",
        "skillSet": [
          "Anaesthetics",
          "Nurse"
        ]
      },
      "date": "2024-04-08",
      "availabilityType": "DESIRED"
    }
  ],
  "employeeList": [
    {
      "name": "Amy King",
      "skillSet": [
        "Cardiology",
        "Anaesthetics",
        "Nurse"
      ]
    }
  ],
  "shiftList": [
    {
      "id": 4,
      "start": "2024-04-08T06:00:00",
      "end": "2024-04-08T14:00:00",
      "location": "Ambulatory care",
      "requiredSkill": "Anaesthetics",
      "employee": null
    }
  ],

  "score": "-133init/0hard/0soft",
  "scheduleState": {
    "tenantId": 1,
    "publishLength": 7,
    "draftLength": 14,
    "firstDraftDate": "2024-04-08",
    "lastHistoricDate": "2024-04-01",
    "firstUnplannedDate": "2024-04-22"
  },
  "solverStatus": "NOT_SOLVING"
}';

    const EMPTYSCHEDULE = '{
  "availabilityList": [
  ],
  "employeeList": [
  ],
  "shiftList": [
  ],
  "solverStatus": "NOT_SOLVING"
}';

    public function testAdd()
    {
//        $this->assertTrue(true);

        $host = config('solver.solver_hosts.roster');

        echo "address=" . $host . "\n";

        /** @var SolverClientFactory $factory */
        $factory = app(SolverClientFactory::class);

        $client = $factory->createClient('roster');
        $this->assertTrue($client instanceof SolverClientRoster);


        $rez = $client->registerData(self::MINIMAL);
        $this->assertTrue(is_numeric($rez));
    }
}