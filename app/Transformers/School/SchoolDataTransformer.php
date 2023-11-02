<?php

namespace App\Transformers\School;

use App\Exceptions\ValidateException;
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
        $lessonList = $this->mapToExcelData(self::LESSON_DATA_HEADER_MAP, $jsonData['lessonList']);
        $this->fixExcelLessonList($lessonList);
        return [
            'timeslotList' => $this->mapToExcelData(self::TIMESLOT_DATA_HEADER_MAP, $jsonData['timeslotList']),
            'roomList' => $this->mapToExcelData(self::ROOM_DATA_HEADER_MAP, $jsonData['roomList']),
            'lessonList' => $lessonList,
        ];
    }

    public function excelToJson(array $excelData): array
    {
        $this->validateExcelData($excelData);

        $lessonList = $this->mapToJsonDataByHeaderMap(
            self:: LESSON_DATA_HEADER_MAP,
            $excelData['lessonList']
        );

        $timeslotList = $this->mapToJsonDataByHeaderMap(
            self:: TIMESLOT_DATA_HEADER_MAP,
            $excelData['timeslotList']
        );

        $roomList = $this->mapToJsonDataByHeaderMap(
            self:: ROOM_DATA_HEADER_MAP,
            $excelData['roomList']
        );

        $this->fixJsonLessonList($lessonList, $timeslotList, $roomList);

        return [
            'timeslotList' => $timeslotList,
            'roomList' => $roomList,
            'lessonList' => $lessonList,
        ];
    }

    private function fixJsonLessonList(array &$lessonList, array $timeslotList, array $roomList): void
    {
        $timeslotMap = MapBuilder::buildMap($timeslotList, fn($timeslotRow) => $timeslotRow['id']);
        $roomsMap = MapBuilder::buildMap($roomList, fn($roomRow) => $roomRow['id']);


        foreach ($lessonList as &$lessonRow) {
            if (empty($lessonRow['timeslot'])) {
                $lessonRow['timeslot'] = null;
            }
            if (empty($lessonRow['room'])) {
                $lessonRow['room'] = null;
            }

            $timeslotId = ExcelParser::extractId($lessonRow['timeslot']);
            if ($timeslotId != null) {
                if (array_key_exists($timeslotId, $timeslotMap)) {
                    $lessonRow['timeslot'] = $timeslotMap[$timeslotId];
                }
            }

            $roomId = ExcelParser::extractId($lessonRow['room']);

            if ($roomId != null) {
                if (array_key_exists($roomId, $roomsMap)) {
                    $lessonRow['room'] = $roomsMap[$roomId];
                }
            }
        }
    }

    public function mapToJsonDataByHeaderMap(array $headerMap, array $sourceDataList): array
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

    public function mapToExcelData(array $headerMap, array $jsonDataList): array
    {
        $excelHeader = array_keys($headerMap);
        $excelData = [];
        $excelData[] = $excelHeader;

        foreach ($jsonDataList as $jsonRow) {
            $excelRow = [];
            foreach ($headerMap as $jsonProperty) {
                $excelRow[] = $jsonRow[$jsonProperty];
            }
            $excelData[] = $excelRow;
        }

        return $excelData;
    }

    private function fixExcelLessonList(array &$lessonList): void
    {
        $jsonKeys = array_values (self::LESSON_DATA_HEADER_MAP);
        $jsonKeySet = array_flip($jsonKeys);

        $timeslotIndex=$jsonKeySet['timeslot'];
        $roomIndex = $jsonKeySet['room'];

        for ($row = 1; $row < count($lessonList); $row++) {
            if (is_array($lessonList[$row][$timeslotIndex])) {
                $timeslot = $lessonList[$row][$timeslotIndex];
                $lessonList[$row][$timeslotIndex] = sprintf(
                    '[%s] %s %s-%s',
                    $timeslot['id'],
                    $timeslot['dayOfWeek'],
                    $timeslot['startTime'],
                    $timeslot['endTime']
                );
            }

            if (is_array($lessonList[$row][$roomIndex])) {
                $room = $lessonList[$row][$roomIndex];
                $lessonList[$row][$roomIndex] = sprintf('[%s] %s', $room['id'], $room['name']);
            }
        }
    }

    private function validateExcelData(array $excelData) {
        $mustHeadersList = [
            'timeslotList' => array_keys(self::TIMESLOT_DATA_HEADER_MAP),
            'roomList' => array_keys(self::ROOM_DATA_HEADER_MAP),
            'lessonList' => array_keys(self::LESSON_DATA_HEADER_MAP),
        ];

        foreach ( $mustHeadersList as $sheetName => $mustHeaders ) {
            if (!array_key_exists($sheetName, $excelData)) {
                throw new ValidateException(sprintf('missing sheet [%s]', $sheetName));
            }
            if ( !is_array($excelData[$sheetName]) || count($excelData[$sheetName]) == 0) {
                throw new ValidateException(sprintf('sheet [%s] has no header', $sheetName));
            }

            $this->validateSheetHeader($sheetName, $excelData[$sheetName][0], $mustHeaders);
        }
    }

    private function validateSheetHeader ( $sheetName, $sheetHeader, $mustHeaders ) : void {
        foreach ($mustHeaders as $mustHeader ) {
            if ( !in_array( $mustHeader, $sheetHeader) ) {
                throw new ValidateException(sprintf('%s sheet missing column [%s]', $sheetName,  $mustHeader ));
            }
        }
    }
}