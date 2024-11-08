<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\SubjectData;
use App\Domain\Roster\SubjectsContainer;
use PHPUnit\Framework\TestCase;

class UnpackSubjectsTest extends TestCase
{
    /**
     * @dataProvider provideSubjectsDatas
     */
    public function testUnpack(string $json, SubjectsContainer $expectedSubjectsArray)
    {
        $arrayData = json_decode($json, true);
        $subjectsArray = new SubjectsContainer($arrayData);
        $this->assertEquals($expectedSubjectsArray, $subjectsArray);
    }

    public static function provideSubjectsDatas(): array
    {
        return [
            'test1' => [
                'json' => '{"subjects" :  [ { "name" : "Jonas", "position_amount": 0.75, "hours_in_month":73 }] }',
                'expectedSubjectsArray' =>
                    (new SubjectsContainer())
                        ->setSubjects(
                            [
                                (new SubjectData())
                                    ->setName("Jonas")
                                    ->setPositionAmount(0.75)
                                    ->setHoursInMonth(73)
                            ]
                        )

            ]
        ];
    }
}