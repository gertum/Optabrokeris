<?php

namespace App\Solver;

class SolverClientFactory
{
    public const TYPE_SCHOOL = 'school';
    public const TYPE_ROSTER = 'roster';

    public const TYPES = [
        self::TYPE_SCHOOL,
        self::TYPE_ROSTER
    ];

    public function createClient($type): SolverClient
    {
        $hosts = config('solver.solver_hosts');

        return match ($type) {
            self::TYPE_SCHOOL => new SolverClientSchool($type, $hosts[$type]),
            self::TYPE_ROSTER => new SolverClientRoster($type, $hosts[$type]),
            default => throw new \Exception(sprintf('Solver type %s is not implemented', $type))
        };
    }
}
