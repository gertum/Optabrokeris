<?php

namespace App\Http\Controllers\Api;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectController
{
    public function view(Subject $subject)
    {
        return $subject;
    }

    public function list(Request $request)
    {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 50);
        return Subject::query()->offset($offset)->limit($limit)->get()->all();
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'position_amount' => 'required|numeric',
                'hours_in_month' => 'required|numeric',
            ]
        );
        $validated = $validator->validate();

        $subject = Subject::query()->newModelInstance();
        return $subject->create($validated);
    }

    public function update(Request $request, Subject $subject)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'position_amount' => 'required|numeric',
                'hours_in_month' => 'required|numeric',
            ]
        );

        $subject->update($validator->validated());

        return $subject;
    }

    public function delete(Subject $subject)
    {
        $subject->delete();

        return $subject;
    }

    public function upsertJson(Request $request)
    {
    }

    public function upsertXslx(Request $request)
    {
    }

    public function upsertCsv(Request $request)
    {
    }

}