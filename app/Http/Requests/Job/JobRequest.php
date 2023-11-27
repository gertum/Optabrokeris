<?php

namespace App\Http\Requests\Job;

use App\Models\Job;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class JobRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string',
        ];
    }

    public function getUserJob($id): Job
    {
        $userId = $this->user()->id;
        $job = Job::query()->user($userId)->find($id);

        if ($job == null) {
            throw new NotFoundHttpException(sprintf('Cant find a job with id %s', $id));
        }

        return $job;
    }
}
