<?php

namespace App\Transformers;

use PhpParser\Node\Expr\Array_;
use Shuchkin\SimpleXLSX;
use Exception;

class ExcelDataHandler
{
    public function loadSchoolDataFromExcel(string $excelFile): array
    {

        if ($xlsx = SimpleXLSX::parse($excelFile)) {
//            print_r($xlsx->rows());
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

            $transformedExcelData = array(
                "timeslotList" => $timeslotList,
                "roomList" => $roomList,
                "lessonList" => $lessonList
            );
            // TODO multiple requests from xls for each sheet
            return $transformedExcelData;
        }
        throw new Exception(SimpleXLSX::parseError());

    }
}