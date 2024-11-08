<?php

namespace App\Repositories;

use App\Domain\Roster\SubjectDataInterface;
use App\Models\Subject;
use App\Util\UpsertQueryBuilder;
use Illuminate\Support\Facades\DB;

class SubjectRepository
{
    /**
     * @param SubjectDataInterface[] $subjects
     */
    public function upsertSubjectsDatas(array $subjects) {
        $pdo = DB::getPdo();

        $data = array_map ( fn(SubjectDataInterface $subject)=>$subject->toArray(), $subjects);
        $query = UpsertQueryBuilder::buildUpsertQueryFromDataArraysForMysql(
            fn($str)=>$pdo->quote($str),
            $data,
            'subjects',
            ['name','position_amount','hours_in_month'],
            ['position_amount','hours_in_month'],
        );

        return $pdo->exec($query);
    }

    /**
     * @param string[] $names
     * @return Subject[]
     */
    public function loadSubjectsByNames(array $names): array {
        return Subject::query()->whereIn('name', $names)->get();
    }
}