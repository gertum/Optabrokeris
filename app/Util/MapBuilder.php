<?php

namespace App\Util;

/**
 * Indexer.
 */
class MapBuilder
{
    public static function buildMap(array $objects, callable $keyGetter) : array {
        $map = [];
        foreach ($objects as $object) {
            $key = call_user_func($keyGetter, $object);
            $map[$key] = $object;
        }

        return $map;
    }

    public static function buildMapWithValue(array $objects, callable $keyGetter, callable $valueGetter) : array {
        $map = [];
        foreach ($objects as $object) {
            $key = call_user_func($keyGetter, $object);
            $value = call_user_func($valueGetter, $object);
            $map[$key] = $value;
        }

        return $map;
    }
}