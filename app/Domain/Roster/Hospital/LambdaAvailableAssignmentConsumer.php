<?php

namespace App\Domain\Roster\Hospital;

use App\Domain\Roster\Employee;
use Closure;

/**
 * @Deprecated impossible to unit test.
 */
class LambdaAvailableAssignmentConsumer implements AvailableAssignmentConsumer
{
    private Closure $lambda;

    /**
     * @param Closure $lambda - should have two parameters: from and till dates as string representations
     */
    public function __construct(Closure $lambda)
    {
        $this->lambda = $lambda;
    }

    public function setAssignment(string $from, string $till, Employee $employee): void
    {
        call_user_func($this->lambda, $from, $till);
    }
}