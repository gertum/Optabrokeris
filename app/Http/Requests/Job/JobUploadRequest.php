<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

class JobUploadRequest extends FormRequest
{
    public function rules()
    {
        return [
            'file' => 'required|file',
        ];
    }
}
