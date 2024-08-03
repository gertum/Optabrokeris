<?php

namespace App\Repositories;

use App\Models\Job;

class JobRepository
{
    public function getJob($id) : ? Job {
        return Job::query()->find($id)->get()->first();
    }
}