<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

class JobSolveRequest extends FormRequest
{
    public function rules()
    {
        return [
            'repeat' => 'boolean',
        ];
    }
}
