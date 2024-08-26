<?php

namespace App\Solver;

interface SolverClient
{
    public function registerData($data): int;

    public function startSolving($solverId): string;
    public function stopSolving($solverId): string;

    public function getResult($solverId): string;

    public function getType(): string;
}