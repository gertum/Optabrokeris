<?php

namespace App\Util;

class BinarySearch
{
    /**
     * Should be faster than 'in_array', when shifts array is big. Also makes possibility to search object by its property.
     *
     * @param array $array Some objects or values array, ordered in increasing order.
     * @param mixed $value Value to search.
     * @param callable $comparator Must be able to compare object from array to the given value: returns 0 if equal, -1 if the value is lesser, +1 if the value is greater than the compared object.
     * @return int Found index, -1 if not found.
     */
    public static function search(array $array, mixed $value, callable $comparator): int
    {
        if (count($array) === 0) {
            return false;
        }
        $low = 0;
        $high = count($array) - 1;

        while ($low <= $high) {
            // compute middle index
            $mid = floor(($low + $high) / 2);

            $comparedValue = call_user_func($comparator, $array[$mid], $value);

            if ($comparedValue == 0) {
                return $mid;
            }

            if ($comparedValue < 0) {
                $high = $mid - 1;
                continue;
            }

            // $comparedValue > 0
            $low = $mid + 1;
        }

        return -1;
    }
}