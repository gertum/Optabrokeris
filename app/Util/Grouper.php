<?php

namespace App\Util;

class Grouper
{
    public static function group(array $data, callable $keyGetter): array
    {
        $groups = [];

        foreach ($data as $element) {
            $key = call_user_func($keyGetter, $element);
            if (!array_key_exists($key, $groups)) {
                $groups[$key] = [];
            }
            $groups[$key][] = $element;
        }

        return $groups;
    }
}