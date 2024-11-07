<?php

namespace App\Repositories;

use App\Data\SubjectData;
use App\Models\Subject;
use App\Util\UpsertQueryBuilder;
use Illuminate\Support\Facades\DB;

class SubjectRepository
{
    /**
     * @param Subject[] $subjects
     */
    public function upsertSubjectsDatas(array $subjects) {
        $pdo = DB::getPdo();

        $data = array_map ( fn(SubjectData $subject)=>$subject->toArray(), $subjects);
        $query = UpsertQueryBuilder::buildUpsertQueryFromDataArraysForMysql(
            fn($str)=>$pdo->quote($str),
            $data,
            'subjects',
            ['name','position_amount','hours_in_month'],
            ['position_amount','hours_in_month'],
        );

        return $pdo->exec($query);
    }
}