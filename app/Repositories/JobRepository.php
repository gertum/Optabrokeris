<?php

namespace App\Repositories;

use App\Models\Job;

class JobRepository
{
    public function getJob($id): ?Job
    {
        return Job::query()->find($id)->get()->first();
    }

    public function findJobByName(string $name): ?Job
    {
        $jobs = Job::query()->where(['name' => $name]);

        return $jobs->first();
    }

    public function getJobList($userId, int $offset=0, int $limit=20)
    {
        // TODO use offset and limit
        $jobs = Job::query()
            ->select(
                [
                    // we skip 'data' and 'result'
                    'id',
                    'created_at',
                    'updated_at',
                    'user_id',
                    'solver_id',
                    'status',
                    'type',
                    'name',
                    'flag_uploaded',
                    'flag_solving',
                    'flag_solved'
                ]
            )
            ->orderByDesc('created_at')
            ->user($userId)
            ->get()
            ;

        return $jobs;
    }
}