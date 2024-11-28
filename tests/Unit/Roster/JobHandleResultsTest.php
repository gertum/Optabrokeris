<?php

namespace Tests\Unit\Roster;

use App\Models\Job;
use Tests\TestCase;

/**
 * Lets try to make a laravel test.
 * Might be possible to run with php artisan test command only, so naming should be modified in that case.
 */
class JobHandleResultsTest extends TestCase
{
    /**
     * @dataProvider provideDataForJobResults
     */
    public function testHandleResult(string $result, string $errorMessage, Job $job, Job $expectedJobWithAttributes)
    {
        $job->handleResultFromSolver($result, $errorMessage);

        $this->assertEquals($expectedJobWithAttributes->getType(), $job->getType());
        $this->assertEquals($expectedJobWithAttributes->getStatus(), $job->getStatus());
        $this->assertEquals($expectedJobWithAttributes->getFlagSolving(), $job->getFlagSolving());
        $this->assertEquals($expectedJobWithAttributes->getFlagUploaded(), $job->getFlagUploaded());
        $this->assertEquals($expectedJobWithAttributes->getFlagSolved(), $job->getFlagSolved());
    }

    public static function provideDataForJobResults(): array
    {
        return [
            'test1' => [
                'result' => '{}',
                'errorMessage' => '',
                'job' => new Job(),
                'expectedJob' => new Job(),
            ]
        ];
    }
}