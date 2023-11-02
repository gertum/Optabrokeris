<?php

namespace Tests\Unit\School;

use App\Exceptions\ValidateException;
use App\Transformers\School\SchoolDataTransformer;
use PHPUnit\Framework\TestCase;

class SchoolDataTransformerTest extends TestCase
{
    /**
     * @dataProvider provideExcelData
     */
    public function testTransformToJson(
        array $excelData,
        array $expectedJsonData,
    ) {
        $schoolDataTransformer = new SchoolDataTransformer();
        $jsonData = $schoolDataTransformer->excelToJson($excelData);

        $this->assertEquals($expectedJsonData, $jsonData);
    }

    public static function provideExcelData(): array
    {
        return [
            'test1' => [
                'excelData' => include __DIR__ . '/data/excelData.php',
                'expectedJsonData' => include __DIR__ . '/data/jsonData.php',
            ],
            'test2' => [
                'excelData' => array(
                    'timeslotList' =>
                        array(
                            array(
                                'id',
                                'day of week',
                                'start time',
                                'end time',
                            ),
                            array(
                                1,
                                'MONDAY',
                                '08:30:00',
                                '09:30:00',
                            ),
                        ),
                    'roomList' =>
                        [
                            [
                                'id',
                                'name',
                            ],
                            [
                                1,
                                'Room A',
                            ],
                        ],

                    'lessonList' => [
                        [
                            'id',
                            'subject',
                            'teacher',
                            'student group',
                            'timeslot',
                            'room',
                        ],
                        [
                            1,
                            'Math',
                            'A. Turing',
                            '9th grade',
                            '[1] MONDAY 08:30:00-09:30:00',
                            '[1] Room A',
                        ],
                    ],
                ),

                'expectedJsonData' => [
                    'timeslotList' =>
                        [
                            [
                                'id' => 1,
                                'dayOfWeek' => 'MONDAY',
                                'startTime' => '08:30:00',
                                'endTime' => '09:30:00',
                            ],
                        ],
                    'roomList' =>
                        array(
                            array(
                                'id' => 1,
                                'name' => 'Room A',
                            ),
                        ),
                    'lessonList' =>
                        array(
                            array(
                                'id' => 1,
                                'subject' => 'Math',
                                'teacher' => 'A. Turing',
                                'studentGroup' => '9th grade',
                                'timeslot' =>
                                    array(
                                        'id' => 1,
                                        'dayOfWeek' => 'MONDAY',
                                        'startTime' => '08:30:00',
                                        'endTime' => '09:30:00',
                                    ),
                                'room' =>
                                    array(
                                        'id' => 1,
                                        'name' => 'Room A',
                                    ),
                            ),
                        )
                ]
            ],


        ];
    }


    /**
     * @dataProvider provideNonValidExcelData
     */
    public function testInvalidExcelData(
        array $excelData,
        string $expectedException,
        string $expectedExceptionMessage
    ) {
        $this->expectException($expectedException);
        $this->expectExceptionMessageMatches($expectedExceptionMessage);

        $schoolDataTransformer = new SchoolDataTransformer();
        $schoolDataTransformer->excelToJson($excelData);
    }

    public static function provideNonValidExcelData(): array
    {
        return [
            'invalid test 1' => [
                'excelData' => array(
                    'timeslotList' =>
                        array(
                            array(
                                'id',
                                'day of week',
                                'start time',
                                'end times',
                            ),
                            array(
                                1,
                                'MONDAY',
                                '08:30:00',
                                '09:30:00',
                            ),
                        ),
                    'roomList' =>
                        [
                            [
                                'id',
                                'name',
                            ],
                            [
                                1,
                                'Room A',
                            ],
                        ],

                    'lessonList' => [
                        [
                            'id',
                            'subject',
                            'teacher',
                            'student group',
                            'timeslot',
                            'room',
                        ],
                        [
                            1,
                            'Math',
                            'A. Turing',
                            '9th grade',
                            '[1] MONDAY 08:30:00-09:30:00',
                            '[1] Room A',
                        ],
                    ],
                ),
                'expectedException' => ValidateException::class,
                'expectedExceptionMessage' => '/missing column \[end time\]/i'
            ],
            'invalid test 2' => [
                'excelData' => array(
                    'roomList' =>
                        [
                            [
                                'id',
                                'name',
                            ],
                            [
                                1,
                                'Room A',
                            ],
                        ],

                    'lessonList' => [
                        [
                            'id',
                            'subject',
                            'teacher',
                            'student group',
                            'timeslot',
                            'room',
                        ],
                        [
                            1,
                            'Math',
                            'A. Turing',
                            '9th grade',
                            '[1] MONDAY 08:30:00-09:30:00',
                            '[1] Room A',
                        ],
                    ],
                ),
                'expectedException' => ValidateException::class,
                'expectedExceptionMessage' => '/missing sheet \[timeslotList\]/i'
            ],
            'invalid test 3' => [
                'excelData' => array(
                    'timeslotList' =>
                        array(
                            array(
                                'id',
                                'day of week',
                                'start time',
                                'end time',
                            ),
                            array(
                                1,
                                'MONDAY',
                                '08:30:00',
                                '09:30:00',
                            ),
                        ),
                    'roomList' =>
                        [
                        ],

                    'lessonList' => [
                        [
                            'id',
                            'subject',
                            'teacher',
                            'student group',
                            'timeslot',
                            'room',
                        ],
                        [
                            1,
                            'Math',
                            'A. Turing',
                            '9th grade',
                            '[1] MONDAY 08:30:00-09:30:00',
                            '[1] Room A',
                        ],
                    ],
                ),
                'expectedException' => ValidateException::class,
                'expectedExceptionMessage' => '/sheet \[roomList\] has no header/i'
            ],
        ];
    }
}