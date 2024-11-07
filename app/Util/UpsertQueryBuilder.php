<?php

namespace App\Util;

use Closure;

class UpsertQueryBuilder
{
    public static function buildUpsertQueryFromDataArraysForMysql(
        Closure $quoter,
        array $dataArrays,
        string $tableName,
        array $columnNames,
        array $updatedColumnNames
    ) {
        $collectedValuesLines = [];

        foreach ($dataArrays as $dataArray) {
            $values = self::getValuesByGivenOrderedKeys($dataArray, $columnNames);
            $quotedValues = array_map($quoter, $values);
            $collectedValuesLines[] = sprintf('(%s)', join(',', $quotedValues));
        }

        $valuesString = join(",\n", $collectedValuesLines);
        // back quotes might be needed
        $columnNamesStr = join(',', $columnNames);


        $updateColumnLines = [];
        foreach ($updatedColumnNames as $columnName) {
            $updateColumnLines[] = sprintf('%s = values(%s)', $columnName, $columnName);
        }

        $updates = join(",\n", $updateColumnLines);

        $updatePart = '';
        if (count($updateColumnLines) > 0) {
            $updatePart = sprintf(
                'ON duplicate key update
                        %s',
                $updates
            );
        }

        return sprintf(
            "INSERT INTO %s (%s)
                        VALUES %s
                        %s",
            $tableName,
            $columnNamesStr,
            $valuesString,
            $updatePart
        );
    }

    public static function getValuesByGivenOrderedKeys(array $valuesMap, array $keys)
    {
        $values = [];
        foreach ($keys as $key) {
            $values[] = $valuesMap[$key] ?? null;
        }

        return $values;
    }


    public static function buildUpsertQueryFromDataArraysForMSSQL(
        Closure $quoter,
        array $dataArrays,
        string $tableName,
        array $columnNames,
        array $conditionColumnNames,
        array $updatedColumnNames,
        bool $useInsert = true
    ) {
        $tmpAlias = 'foo';

        $collectedValuesLines = [];

        foreach ($dataArrays as $dataArray) {
            $values = self::getValuesByGivenOrderedKeys($dataArray, $columnNames);
            $quotedValues = array_map($quoter, $values);
            $collectedValuesLines[] = sprintf('(%s)', join(', ', $quotedValues));
        }

        $valuesString = join(",\n", $collectedValuesLines);
        // back quotes might be needed
        $columnNamesStr = join(', ', $columnNames);

        $updateColumnLines = [];
        foreach ($updatedColumnNames as $columnName) {
            $updateColumnLines[] = sprintf('%s = %s.%s', $columnName, $tmpAlias, $columnName);
        }

        $updates = join(",\n", $updateColumnLines);

        $conditionChecks = array_map(
            fn($columnName) => sprintf("%s.%s = %s.%s", $tableName, $columnName, $tmpAlias, $columnName),
            $conditionColumnNames
        );
        $conditionChecksStr = join("\nAND ", $conditionChecks);

        $insertFields = array_map(fn($columnName) => sprintf("%s.%s", $tmpAlias, $columnName), $columnNames);
        $insertFieldsStr = join(", ", $insertFields);

        $insertPart = "";
        if ($useInsert) {
            $insertPart = "WHEN NOT MATCHED THEN
INSERT ($columnNamesStr)
VALUES ($insertFieldsStr)";
        }

        return "MERGE $tableName
USING (
VALUES
$valuesString
) AS $tmpAlias ($columnNamesStr)
ON $conditionChecksStr
WHEN MATCHED THEN
UPDATE SET $updates
$insertPart
;";
    }
}
