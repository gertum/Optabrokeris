<?php

namespace App\Transformers;

use Exception;
use Shuchkin\SimpleXLSX;

class ExcelDataHandler
{
    public function loadSchoolDataFromExcel(string $excelFile): array
    {

        if ($xlsx = SimpleXLSX::parse($excelFile)) {

            $timeslotList = $xlsx->rows(0);
            //make arrays associative instead of indexed
            $timeslotList = array_map(
                function ($transformedExcelColumn) {
                    return ['id' => $transformedExcelColumn[0],
                        'dayOfWeek' => $transformedExcelColumn[1],
                        'startTime' => $transformedExcelColumn[2],
                        'endTime' => $transformedExcelColumn[3]
                    ];
                }, $timeslotList
            );


            $roomList = $xlsx->rows(1);
            $roomList = array_map(
                function ($transformedExcelColumn) {
                    return ['id' => $transformedExcelColumn[0],
                        'name' => $transformedExcelColumn[1]
                    ];
                }, $roomList
            );

            $lessonList = $xlsx->rows(2);
            $lessonList = array_map(
                function ($transformedExcelColumn) {
                    return ['id' => $transformedExcelColumn[0],
                        'subject' => $transformedExcelColumn[1],
                        'teacher' => $transformedExcelColumn[2],
                        'studentGroup' => $transformedExcelColumn[3],
                        'timeslot' => $transformedExcelColumn[4],
                        'room' => $transformedExcelColumn[5]
                    ];
                }, $lessonList
            );
            //json format wants repetition
            for ($i = 0; $i < count($lessonList); $i++) {
                $lesson = $lessonList[$i];
                if ($lesson["timeslot"] != null) {
                    $timeslotId = $lesson["timeslot"];
                    for ($j = 0; $j < count($timeslotList); $j++) {
                        if ($timeslotList[$j]["id"] == $timeslotId) {
                            $lessonList[$i]["timeslot"] = $timeslotList[$j];
                            break;
                        }
                    }

                }
                if ($lesson["room"] != null) {
                    $roomId = $lesson["room"];
                    for ($j = 0; $j < count($roomList); $j++) {
                        if ($roomList[$j]["id"] == $roomId) {
                            $lessonList[$i]["room"] = $roomList[$j];
                            break;
                        }
                    }
                }
            }


            $transformedExcelData = array(
                "timeslotList" => $timeslotList,
                "roomList" => $roomList,
                "lessonList" => $lessonList
            );
            return $transformedExcelData;
        }
        throw new Exception(SimpleXLSX::parseError());

    }
}