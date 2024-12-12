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
        $this->assertEquals($expectedJobWithAttributes->getErrorMessage(), $job->getErrorMessage());
        $this->assertEquals($expectedJobWithAttributes->getResult(), $job->getResult());
    }

    public static function provideDataForJobResults(): array
    {
        return [
            'test empty' => [
                'result' => '{}',
                'errorMessage' => '',
                'job' => new Job(),
                'expectedJob' => (new Job())->setResult('{}'),
            ],
            'test error variant' => [
                'result' => '{"solverStatus": "NOT_SOLVING"}',
                'errorMessage' => 'abc',
                'job' => (new Job())
                    ->setFlagUploaded(true)
                    ->setFlagSolving(true)
                    ->setFlagSolved(false)
                ,
                'expectedJob' => (new Job())
                    ->setErrorMessage('abc')
                    ->setFlagUploaded(true)
                    ->setFlagSolving(false)
                    ->setFlagSolved(false)
                    ->setStatus('NOT_SOLVING')
                    ->setResult('{"solverStatus": "NOT_SOLVING"}')
                ,
            ],
            'test solving not started' => [
                'result' => '{"solverStatus": "NOT_SOLVING"}',
                'errorMessage' => '',
                'job' => (new Job())
                    ->setFlagUploaded(true)
                    ->setFlagSolving(false)
                    ->setFlagSolved(false)
                ,
                'expectedJob' => (new Job())
                    ->setErrorMessage('')
                    ->setFlagUploaded(true)
                    ->setFlagSolving(false)
                    ->setFlagSolved(false)
                    ->setStatus('NOT_SOLVING')
                    ->setResult('{"solverStatus": "NOT_SOLVING"}')
                ,
            ],
            'test solving started' => [
                'result' => '{"solverStatus": "SOLVING"}',
                'errorMessage' => '',
                'job' => (new Job())
                    ->setFlagUploaded(true)
                    ->setFlagSolving(true)
                    ->setFlagSolved(false)
                ,
                'expectedJob' => (new Job())
                    ->setErrorMessage('')
                    ->setFlagUploaded(true)
                    ->setFlagSolving(true)
                    ->setFlagSolved(false)
                    ->setStatus('SOLVING')
                    ->setResult('{"solverStatus": "SOLVING"}')
                ,
            ],
            'test solving finished successfully' => [
                'result' => '{"solverStatus": "NOT_SOLVING"}',
                'errorMessage' => '',
                'job' => (new Job())
                    ->setFlagUploaded(true)
                    ->setFlagSolving(true)
                    ->setFlagSolved(false)
                ,
                'expectedJob' => (new Job())
                    ->setErrorMessage('')
                    ->setFlagUploaded(true)
                    ->setFlagSolving(true)
                    ->setFlagSolved(true)
                    ->setStatus('NOT_SOLVING')
                    ->setResult('{"solverStatus": "NOT_SOLVING"}'
                    )
                ,
            ],
        ];
    }
}