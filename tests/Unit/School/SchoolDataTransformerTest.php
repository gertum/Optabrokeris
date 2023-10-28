<?php

namespace Tests\Unit\School;

use App\Transformers\School\SchoolDataTransformer;
use PHPUnit\Framework\TestCase;

class SchoolDataTransformerTest extends TestCase
{
    /**
     * @dataProvider provideExcelData
     */
    public function testTransformToJson(array $excelData, array $expectedJsonData)
    {
        $schoolDataTransformer = new SchoolDataTransformer();
        $jsonData = $schoolDataTransformer->excelToJson($excelData);

        $this->assertEquals($expectedJsonData, $jsonData);
    }

    public function provideExcelData(): array
    {
        return [
            'test1' => [
                'excelData' => array(
                    'timeslotList' =>
                        array(
                            0 =>
                                array(
                                    0 => 'id',
                                    1 => 'day of week',
                                    2 => 'start time',
                                    3 => 'end time',
                                ),
                            1 =>
                                array(
                                    0 => 1,
                                    1 => 'MONDAY',
                                    2 => '08:30:00',
                                    3 => '09:30:00',
                                ),
                            2 =>
                                array(
                                    0 => 2,
                                    1 => 'MONDAY',
                                    2 => '09:30:00',
                                    3 => '10:30:00',
                                ),
                            3 =>
                                array(
                                    0 => 3,
                                    1 => 'MONDAY',
                                    2 => '10:30:00',
                                    3 => '11:30:00',
                                ),
                            4 =>
                                array(
                                    0 => 4,
                                    1 => 'MONDAY',
                                    2 => '13:30:00',
                                    3 => '14:30:00',
                                ),
                            5 =>
                                array(
                                    0 => 5,
                                    1 => 'MONDAY',
                                    2 => '14:30:00',
                                    3 => '15:30:00',
                                ),
                            6 =>
                                array(
                                    0 => 6,
                                    1 => 'TUESDAY',
                                    2 => '08:30:00',
                                    3 => '09:30:00',
                                ),
                            7 =>
                                array(
                                    0 => 7,
                                    1 => 'TUESDAY',
                                    2 => '09:30:00',
                                    3 => '10:30:00',
                                ),
                            8 =>
                                array(
                                    0 => 8,
                                    1 => 'TUESDAY',
                                    2 => '10:30:00',
                                    3 => '11:30:00',
                                ),
                            9 =>
                                array(
                                    0 => 9,
                                    1 => 'TUESDAY',
                                    2 => '13:30:00',
                                    3 => '14:30:00',
                                ),
                            10 =>
                                array(
                                    0 => 10,
                                    1 => 'TUESDAY',
                                    2 => '14:30:00',
                                    3 => '15:30:00',
                                ),
                        ),
                    'roomList' =>
                        array(
                            0 =>
                                array(
                                    0 => 'id',
                                    1 => 'name',
                                ),
                            1 =>
                                array(
                                    0 => 1,
                                    1 => 'Room A',
                                ),
                            2 =>
                                array(
                                    0 => 2,
                                    1 => 'Room B',
                                ),
                            3 =>
                                array(
                                    0 => 3,
                                    1 => 'Room C',
                                ),
                        ),
                    'lessonList' =>
                        array(
                            0 =>
                                array(
                                    0 => 'id',
                                    1 => 'subject',
                                    2 => 'teacher',
                                    3 => 'student group',
                                    4 => 'timeslot',
                                    5 => 'room',
                                ),
                            1 =>
                                array(
                                    0 => 1,
                                    1 => 'Math',
                                    2 => 'A. Turing',
                                    3 => '9th grade',
                                    4 => '[1] MONDAY 08:30:00-09:30:00',
                                    5 => '[1] Room A',
                                ),
                            2 =>
                                array(
                                    0 => 2,
                                    1 => 'Math',
                                    2 => 'A. Turing',
                                    3 => '9th grade',
                                    4 => '',
                                    5 => '',
                                ),
                            3 =>
                                array(
                                    0 => 3,
                                    1 => 'Physics',
                                    2 => 'M. Curie',
                                    3 => '9th grade',
                                    4 => '',
                                    5 => '',
                                ),
                            4 =>
                                array(
                                    0 => 4,
                                    1 => 'Chemistry',
                                    2 => 'M. Curie',
                                    3 => '9th grade',
                                    4 => '',
                                    5 => '',
                                ),
                            5 =>
                                array(
                                    0 => 5,
                                    1 => 'Biology',
                                    2 => 'C. Darwin',
                                    3 => '9th grade',
                                    4 => '',
                                    5 => '',
                                ),
                            6 =>
                                array(
                                    0 => 6,
                                    1 => 'History',
                                    2 => 'I. Jones',
                                    3 => '9th grade',
                                    4 => '',
                                    5 => '',
                                ),
                            7 =>
                                array(
                                    0 => 7,
                                    1 => 'English',
                                    2 => 'I. Jones',
                                    3 => '9th grade',
                                    4 => '',
                                    5 => '',
                                ),
                            8 =>
                                array(
                                    0 => 8,
                                    1 => 'English',
                                    2 => 'I. Jones',
                                    3 => '9th grade',
                                    4 => '',
                                    5 => '',
                                ),
                            9 =>
                                array(
                                    0 => 9,
                                    1 => 'Spanish',
                                    2 => 'P. Cruz',
                                    3 => '9th grade',
                                    4 => '',
                                    5 => '',
                                ),
                            10 =>
                                array(
                                    0 => 10,
                                    1 => 'Spanish',
                                    2 => 'P. Cruz',
                                    3 => '9th grade',
                                    4 => '',
                                    5 => '',
                                ),
                            11 =>
                                array(
                                    0 => 11,
                                    1 => 'Math',
                                    2 => 'A. Turing',
                                    3 => '10th grade',
                                    4 => '',
                                    5 => '',
                                ),
                            12 =>
                                array(
                                    0 => 12,
                                    1 => 'Math',
                                    2 => 'A. Turing',
                                    3 => '10th grade',
                                    4 => '',
                                    5 => '',
                                ),
                            13 =>
                                array(
                                    0 => 13,
                                    1 => 'Math',
                                    2 => 'A. Turing',
                                    3 => '10th grade',
                                    4 => '',
                                    5 => '',
                                ),
                            14 =>
                                array(
                                    0 => 14,
                                    1 => 'Physics',
                                    2 => 'M. Curie',
                                    3 => '10th grade',
                                    4 => '',
                                    5 => '',
                                ),
                            15 =>
                                array(
                                    0 => 15,
                                    1 => 'Chemistry',
                                    2 => 'M. Curie',
                                    3 => '10th grade',
                                    4 => '',
                                    5 => '',
                                ),
                            16 =>
                                array(
                                    0 => 16,
                                    1 => 'French',
                                    2 => 'M. Curie',
                                    3 => '10th grade',
                                    4 => '',
                                    5 => '',
                                ),
                            17 =>
                                array(
                                    0 => 17,
                                    1 => 'Geography',
                                    2 => 'C. Darwin',
                                    3 => '10th grade',
                                    4 => '',
                                    5 => '',
                                ),
                            18 =>
                                array(
                                    0 => 18,
                                    1 => 'History',
                                    2 => 'I. Jones',
                                    3 => '10th grade',
                                    4 => '',
                                    5 => '',
                                ),
                            19 =>
                                array(
                                    0 => 19,
                                    1 => 'English',
                                    2 => 'P. Cruz',
                                    3 => '10th grade',
                                    4 => '',
                                    5 => '',
                                ),
                            20 =>
                                array(
                                    0 => 20,
                                    1 => 'Spanish',
                                    2 => 'P. Cruz',
                                    3 => '10th grade',
                                    4 => '',
                                    5 => '',
                                ),
                        ),
                ),

                'expectedJsonData' => array(
                    'timeslotList' =>
                        array(
                            0 =>
                                array(
                                    'id' => 1,
                                    'dayOfWeek' => 'MONDAY',
                                    'startTime' => '08:30:00',
                                    'endTime' => '09:30:00',
                                ),
                            1 =>
                                array(
                                    'id' => 2,
                                    'dayOfWeek' => 'MONDAY',
                                    'startTime' => '09:30:00',
                                    'endTime' => '10:30:00',
                                ),
                            2 =>
                                array(
                                    'id' => 3,
                                    'dayOfWeek' => 'MONDAY',
                                    'startTime' => '10:30:00',
                                    'endTime' => '11:30:00',
                                ),
                            3 =>
                                array(
                                    'id' => 4,
                                    'dayOfWeek' => 'MONDAY',
                                    'startTime' => '13:30:00',
                                    'endTime' => '14:30:00',
                                ),
                            4 =>
                                array(
                                    'id' => 5,
                                    'dayOfWeek' => 'MONDAY',
                                    'startTime' => '14:30:00',
                                    'endTime' => '15:30:00',
                                ),
                            5 =>
                                array(
                                    'id' => 6,
                                    'dayOfWeek' => 'TUESDAY',
                                    'startTime' => '08:30:00',
                                    'endTime' => '09:30:00',
                                ),
                            6 =>
                                array(
                                    'id' => 7,
                                    'dayOfWeek' => 'TUESDAY',
                                    'startTime' => '09:30:00',
                                    'endTime' => '10:30:00',
                                ),
                            7 =>
                                array(
                                    'id' => 8,
                                    'dayOfWeek' => 'TUESDAY',
                                    'startTime' => '10:30:00',
                                    'endTime' => '11:30:00',
                                ),
                            8 =>
                                array(
                                    'id' => 9,
                                    'dayOfWeek' => 'TUESDAY',
                                    'startTime' => '13:30:00',
                                    'endTime' => '14:30:00',
                                ),
                            9 =>
                                array(
                                    'id' => 10,
                                    'dayOfWeek' => 'TUESDAY',
                                    'startTime' => '14:30:00',
                                    'endTime' => '15:30:00',
                                ),
                        ),
                    'roomList' =>
                        array(
                            0 =>
                                array(
                                    'id' => 1,
                                    'name' => 'Room A',
                                ),
                            1 =>
                                array(
                                    'id' => 2,
                                    'name' => 'Room B',
                                ),
                            2 =>
                                array(
                                    'id' => 3,
                                    'name' => 'Room C',
                                ),
                        ),
                    'lessonList' =>
                        array(
                            0 =>
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
                            1 =>
                                array(
                                    'id' => 2,
                                    'subject' => 'Math',
                                    'teacher' => 'A. Turing',
                                    'studentGroup' => '9th grade',
                                    'timeslot' => null,
                                    'room' => null,
                                ),
                            2 =>
                                array(
                                    'id' => 3,
                                    'subject' => 'Physics',
                                    'teacher' => 'M. Curie',
                                    'studentGroup' => '9th grade',
                                    'timeslot' => null,
                                    'room' => null,
                                ),
                            3 =>
                                array(
                                    'id' => 4,
                                    'subject' => 'Chemistry',
                                    'teacher' => 'M. Curie',
                                    'studentGroup' => '9th grade',
                                    'timeslot' => null,
                                    'room' => null,
                                ),
                            4 =>
                                array(
                                    'id' => 5,
                                    'subject' => 'Biology',
                                    'teacher' => 'C. Darwin',
                                    'studentGroup' => '9th grade',
                                    'timeslot' => null,
                                    'room' => null,
                                ),
                            5 =>
                                array(
                                    'id' => 6,
                                    'subject' => 'History',
                                    'teacher' => 'I. Jones',
                                    'studentGroup' => '9th grade',
                                    'timeslot' => null,
                                    'room' => null,
                                ),
                            6 =>
                                array(
                                    'id' => 7,
                                    'subject' => 'English',
                                    'teacher' => 'I. Jones',
                                    'studentGroup' => '9th grade',
                                    'timeslot' => null,
                                    'room' => null,
                                ),
                            7 =>
                                array(
                                    'id' => 8,
                                    'subject' => 'English',
                                    'teacher' => 'I. Jones',
                                    'studentGroup' => '9th grade',
                                    'timeslot' => null,
                                    'room' => null,
                                ),
                            8 =>
                                array(
                                    'id' => 9,
                                    'subject' => 'Spanish',
                                    'teacher' => 'P. Cruz',
                                    'studentGroup' => '9th grade',
                                    'timeslot' => null,
                                    'room' => null,
                                ),
                            9 =>
                                array(
                                    'id' => 10,
                                    'subject' => 'Spanish',
                                    'teacher' => 'P. Cruz',
                                    'studentGroup' => '9th grade',
                                    'timeslot' => null,
                                    'room' => null,
                                ),
                            10 =>
                                array(
                                    'id' => 11,
                                    'subject' => 'Math',
                                    'teacher' => 'A. Turing',
                                    'studentGroup' => '10th grade',
                                    'timeslot' => null,
                                    'room' => null,
                                ),
                            11 =>
                                array(
                                    'id' => 12,
                                    'subject' => 'Math',
                                    'teacher' => 'A. Turing',
                                    'studentGroup' => '10th grade',
                                    'timeslot' => null,
                                    'room' => null,
                                ),
                            12 =>
                                array(
                                    'id' => 13,
                                    'subject' => 'Math',
                                    'teacher' => 'A. Turing',
                                    'studentGroup' => '10th grade',
                                    'timeslot' => null,
                                    'room' => null,
                                ),
                            13 =>
                                array(
                                    'id' => 14,
                                    'subject' => 'Physics',
                                    'teacher' => 'M. Curie',
                                    'studentGroup' => '10th grade',
                                    'timeslot' => null,
                                    'room' => null,
                                ),
                            14 =>
                                array(
                                    'id' => 15,
                                    'subject' => 'Chemistry',
                                    'teacher' => 'M. Curie',
                                    'studentGroup' => '10th grade',
                                    'timeslot' => null,
                                    'room' => null,
                                ),
                            15 =>
                                array(
                                    'id' => 16,
                                    'subject' => 'French',
                                    'teacher' => 'M. Curie',
                                    'studentGroup' => '10th grade',
                                    'timeslot' => null,
                                    'room' => null,
                                ),
                            16 =>
                                array(
                                    'id' => 17,
                                    'subject' => 'Geography',
                                    'teacher' => 'C. Darwin',
                                    'studentGroup' => '10th grade',
                                    'timeslot' => null,
                                    'room' => null,
                                ),
                            17 =>
                                array(
                                    'id' => 18,
                                    'subject' => 'History',
                                    'teacher' => 'I. Jones',
                                    'studentGroup' => '10th grade',
                                    'timeslot' => null,
                                    'room' => null,
                                ),
                            18 =>
                                array(
                                    'id' => 19,
                                    'subject' => 'English',
                                    'teacher' => 'P. Cruz',
                                    'studentGroup' => '10th grade',
                                    'timeslot' => null,
                                    'room' => null,
                                ),
                            19 =>
                                array(
                                    'id' => 20,
                                    'subject' => 'Spanish',
                                    'teacher' => 'P. Cruz',
                                    'studentGroup' => '10th grade',
                                    'timeslot' => null,
                                    'room' => null,
                                ),
                        ),
                    'score' => '-38init/0hard/0soft',
                    'solverStatus' => 'NOT_SOLVING',
                )
            ],
        ];
    }
}