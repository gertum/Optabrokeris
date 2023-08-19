<?php

namespace App\Transformers;

use App\Exceptions\ExcelParseException;
use Illuminate\Support\Carbon;
use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLSXGen;

class ExcelSchoolDataHandler implements SpreadSheetDataHandler
{
    public function spreadSheetToArray(string $excelFile): array
    {
        if ($xlsx = SimpleXLSX::parse($excelFile)) {
            $timeslotList = $xlsx->rows(0);
            //make arrays associative instead of indexed
            $timeslotList = array_map(
                function ($transformedExcelColumn) {
                    return [
                        'id' => $transformedExcelColumn[0],
                        'dayOfWeek' => $transformedExcelColumn[1],
                        'startTime' => Carbon::parse( $transformedExcelColumn[2])->format('H:i:s'),
                        'endTime' => Carbon::parse($transformedExcelColumn[3])->format('H:i:s'),
                    ];
                },
                $timeslotList
            );


            $roomList = $xlsx->rows(1);
            $roomList = array_map(
                function ($transformedExcelColumn) {
                    return [
                        'id' => $transformedExcelColumn[0],
                        'name' => $transformedExcelColumn[1],
                    ];
                },
                $roomList
            );

            $lessonList = $xlsx->rows(2);
            $lessonList = array_map(
                function ($transformedExcelColumn) {
                    return [
                        'id' => $transformedExcelColumn[0],
                        'subject' => $transformedExcelColumn[1],
                        'teacher' => $transformedExcelColumn[2],
                        'studentGroup' => $transformedExcelColumn[3],
                        'timeslot' => $transformedExcelColumn[4],
                        'room' => $transformedExcelColumn[5],
                    ];
                },
                $lessonList
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
                "lessonList" => $lessonList,
            );

            return $transformedExcelData;
        }
        throw new ExcelParseException(SimpleXLSX::parseError());
    }

    public function arrayToSpreadSheet(array $data, string $excelFile): void
    {
        $xlsx = new SimpleXLSXGen();
        $xlsx->addSheet($data["timeslotList"], 'timeslotList');
        $xlsx->addSheet($data["roomList"], 'roomList');
        //excel column only needs id, no need to store whole array

        for ($i = 0; $i < count($data["lessonList"]); $i++) {
            if ($data["lessonList"][$i]["room"] != null) {
                $roomId = $data["lessonList"][$i]["room"]["id"];
                $data["lessonList"][$i]["room"] = $roomId;
            }
            if ($data["lessonList"][$i]["timeslot"] != null) {
                $timeslotId = $data["lessonList"][$i]["timeslot"]["id"];
                $data["lessonList"][$i]["timeslot"] = $timeslotId;
            }
        }
        $xlsx->addSheet($data["lessonList"], 'lessonList');
        $xlsx->saveAs($excelFile);
    }
}
