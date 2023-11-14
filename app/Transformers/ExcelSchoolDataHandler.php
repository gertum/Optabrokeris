<?php

namespace App\Transformers;

use App\Exceptions\ExcelParseException;
use App\Exceptions\SolverDataException;
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
                        'startTime' => Carbon::parse($transformedExcelColumn[2])->format('H:i:s'),
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
                        'timeslot' => $transformedExcelColumn[4] == '' ? null : $transformedExcelColumn[4],
                        'room' => $transformedExcelColumn[5] == '' ? null : $transformedExcelColumn[5],
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
        $this->validateData($data);

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

    private function validateData($data): void
    {
        $topKeys = ['timeslotList', 'roomList', 'lessonList'];

        foreach ($topKeys as $topKey) {
            if (!array_key_exists($topKey, $data)) {
                throw new SolverDataException(sprintf('%s key not found in a data array', $topKey));
            }
        }

        $lessonKeys = ['room', 'timeslot'];
        for ($i = 0; $i < count($data["lessonList"]); $i++) {
            foreach ($lessonKeys as $lessonKey) {
                if (!array_key_exists($lessonKey, $data["lessonList"][$i])) {
                    throw new SolverDataException(
                        sprintf('%s lessonList key not found in lesson line %s', $lessonKey, $i)
                    );
                }

                if (is_array($data["lessonList"][$i][$lessonKey]) &&
                    !array_key_exists('id', $data["lessonList"][$i][$lessonKey])) {
                    throw new SolverDataException(
                        sprintf('id key not found in lessonList elem %s line %s', $lessonKey, $i)
                    );
                }
            }
        }
    }

    public function validateDataArray(array $data): void
    {
        // TODO: Implement validateDataArray() method.
    }
}
