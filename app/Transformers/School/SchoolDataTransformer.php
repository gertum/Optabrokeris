<?php

namespace App\Transformers\School;

use App\Transformers\ExcelParser;
use App\Util\MapBuilder;

class SchoolDataTransformer
{

    const TIMESLOT_DATA_HEADER_MAP = [
        'id' => 'id',
        'day of week' => 'dayOfWeek',
        'start time' => 'startTime',
        'end time' => 'endTime',
    ];

    const ROOM_DATA_HEADER_MAP = [
        'id' => 'id',
        'name' => 'name',
    ];

    const LESSON_DATA_HEADER_MAP = [
        'id' => 'id',
        'subject' => 'subject',
        'teacher' => 'teacher',
        'student group' => 'studentGroup',
        'timeslot' => 'timeslot',
        'room' => 'room',
    ];


    public function jsonToExcel(array $jsonData): array
    {
        // TODO
        return [];
    }

    public function excelToJson(array $excelData): array
    {
        $lessonList = $this->mapDataByHeaderMap(
            self:: LESSON_DATA_HEADER_MAP,
            $excelData['lessonList']
        );

        $timeslotList = $this->mapDataByHeaderMap(
            self:: TIMESLOT_DATA_HEADER_MAP,
            $excelData['timeslotList']
        );

        $roomList = $this->mapDataByHeaderMap(
            self:: ROOM_DATA_HEADER_MAP,
            $excelData['roomList']
        );

        $this->fixJsonLessonList($lessonList, $timeslotList, $roomList );

        return [
            'timeslotList' => $timeslotList,
            'roomList' => $roomList,
            'lessonList' => $lessonList,
        ];
    }

    private function fixJsonLessonList(array & $lessonList, array $timeslotList, array $roomList ): void {
        $timeslotMap = MapBuilder::buildMap($timeslotList, fn($timeslotRow)=>$timeslotRow['id']);
        $roomsMap = MapBuilder::buildMap($roomList, fn($roomRow)=>$roomRow['id']);


        foreach ( $lessonList as &$lessonRow) {
            if ( empty($lessonRow['timeslot'])) {
                $lessonRow['timeslot'] = null;
            }
            if ( empty($lessonRow['room'])) {
                $lessonRow['room'] = null;
            }

            $timeslotId = ExcelParser::extractId($lessonRow['timeslot']);
            if ( $timeslotId != null ) {
                if ( array_key_exists($timeslotId, $timeslotMap)) {
                    $lessonRow['timeslot'] = $timeslotMap[$timeslotId];
                }
            }

            $roomId = ExcelParser::extractId($lessonRow['room']);

            if ( $roomId != null ) {
                if ( array_key_exists($roomId, $roomsMap)) {
                    $lessonRow['room'] = $roomsMap[$roomId];
                }
            }
        }
    }

    public function mapDataByHeaderMap(array $headerMap, array $sourceDataList): array
    {
        $header = $sourceDataList[0];

        $resultDataList = [];
        for ($row = 1; $row < count($sourceDataList); $row++) {
            $resultDataRow = [];
            $timeslotRow = $sourceDataList[$row];
            for ($col = 0; $col < count($timeslotRow); $col++) {
                $columnHeader = $header[$col];
                $value = $timeslotRow[$col];
                $jsonKey = $headerMap[$columnHeader];
                $resultDataRow[$jsonKey] = $value;
            }

            $resultDataList[] = $resultDataRow;
        }

        return $resultDataList;
    }
}