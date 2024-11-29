<?php

namespace App\Solver;

use App\Models\Job;

class SolverClientFactory
{
    public const TYPES = [
        Job::TYPE_SCHOOL,
        Job::TYPE_ROSTER
    ];

    public function createClient($type): SolverClient
    {
        $hosts = config('solver.solver_hosts');

        return match ($type) {
            Job::TYPE_SCHOOL => new SolverClientSchool($type, $hosts[$type]),
            Job::TYPE_ROSTER => new SolverClientRoster($type, $hosts[$type]),
            default => throw new \Exception(sprintf('Solver type %s is not implemented', $type))
        };
    }
}
