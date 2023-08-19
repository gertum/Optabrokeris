<?php

namespace App\Solver;

class SolverClientFactory
{
    public const TYPE_SCHOOL = 'school';

    public const TYPES = [
        self::TYPE_SCHOOL
    ];

    public function createClient($type): SolverClient
    {
        $hosts = config('solver.solver_hosts');

        return match ($type) {
            self::TYPE_SCHOOL => new SolverClientSchool($type, $hosts[$type]),
            default => throw new \Exception(sprintf('Solver type %s is not implemented', $type))
        };
    }
}
