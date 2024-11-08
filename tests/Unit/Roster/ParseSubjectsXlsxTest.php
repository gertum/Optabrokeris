<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Hospital\SubjectsXslsParser;
use App\Domain\Roster\SubjectData;
use App\Domain\Roster\SubjectsContainer;
use PHPUnit\Framework\TestCase;

class ParseSubjectsXlsxTest extends TestCase
{
    /**
     * @dataProvider provideSubjectsFiles
     */
    public function testParse(string $file, SubjectsContainer $expectedSubjectsArray)
    {
        $parser = new SubjectsXslsParser();
        $subjectsArray = $parser->parse($file);
        $this->assertEquals($expectedSubjectsArray, $subjectsArray);
    }

    public static function provideSubjectsFiles(): array
    {
        return [
            'test1' => [
                'file' => __DIR__ . '/data/VULSK_subjects_small.xlsx',
                'expectedSubjectsArray' => (new SubjectsContainer())
                    ->setSubjects(
                        [
                            new SubjectData(
                                [
                                    'name' => 'Aleksandras Briedis',
                                    'position_amount' => 0.25,
                                    'hours_in_day' => 1 + 51 / 60
                                ]
                            ),
                            new SubjectData(
                                [
                                    'name' => 'Vilma Grakauskienė',
                                    'position_amount' => 0.5,
                                    'hours_in_day' => 3 + 42 / 60
                                ]
                            ),
                            new SubjectData(
                                [
                                    'name' => 'Eglė Politikaitė',
                                    'position_amount' => 0.25,
                                    'hours_in_day' => 1 + 51 / 60
                                ]
                            ),
                            new SubjectData(
                                [
                                    'name' => 'Giedrius Montrimas',
                                    'position_amount' => 0.5,
                                    'hours_in_day' => 3 + 42 / 60
                                ]
                            ),
                            new SubjectData(
                                [
                                    'name' => 'Karolis Skaisgirys',
                                    'position_amount' => 0.25,
                                    'hours_in_day' => 1 + 51 / 60
                                ]
                            ),
                        ]
                    )
            ]
        ];
    }
}