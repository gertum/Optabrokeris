<?php

namespace App\Http\Controllers\Api;

use App\Domain\Roster\Hospital\SubjectsXslsParser;
use App\Domain\Roster\SubjectsContainer;
use App\Exceptions\SolverDataException;
use App\Models\Subject;
use App\Repositories\SubjectRepository;
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

    public function upsertJson(Request $request, SubjectRepository $subjectRepository)
    {
        $json = $request->getContent();
        $arrayData = json_decode($json, true);
        $subjectsArray = new SubjectsContainer($arrayData);

        $count = $subjectRepository->upsertSubjectsDatas($subjectsArray->subjects);

        return ["upserted fields" => $count];
    }

    public function upsertXslx(Request $request, SubjectRepository $subjectRepository)
    {
        $file = $request->file('file');

        if ( $file == null ) {
            throw new SolverDataException('In http request missing "file" parameter with xlsx file containing subjects');
        }
        $subjectsXslParser = new SubjectsXslsParser();
        $subjectsContainer = $subjectsXslParser->parse($file->getRealPath());
        $subjectsContainer->recalculateMonthHours(20, true);

        // calculate hours in month
        return $subjectRepository->upsertSubjectsDatas($subjectsContainer->subjects);
    }
}