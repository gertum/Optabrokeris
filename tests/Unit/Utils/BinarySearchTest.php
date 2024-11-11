<?php

namespace Tests\Unit\Utils;

use App\Util\BinarySearch;
use PHPUnit\Framework\TestCase;

class BinarySearchTest extends TestCase
{
    /**
     * @param string[] $data
     *
     * @dataProvider  provideData
     */
    public function testSearch(array $data, string $searchValue, int $expectedIndex, bool $nearestDown, bool $nearestUp)
    {
        $index = BinarySearch::search(
            $data,
            $searchValue,
            fn(string $value, string $searchValue) => $value <=> $searchValue,
            $nearestDown,
            $nearestUp
        );

        $this->assertEquals($expectedIndex, $index);
    }

    public static function provideData(): array
    {
        return [
            'test exact' => [
                'data' => ['A', 'B1', 'B3', 'C'],
                'searchValue' => 'B3',
                'expectedIndex' => 2,
                'nearestDown' => false,
                'nearestUp' => true,
            ],
            'test above' => [
                'data' => ['A', 'B1', 'B3', 'C'],
                'searchValue' => 'B2',
                'expectedIndex' => 2,
                'nearestDown' => false,
                'nearestUp' => true,
            ],
            'test below' => [
                'data' => ['A', 'B1', 'B3', 'C'],
                'searchValue' => 'B2',
                'expectedIndex' => 1,
                'nearestDown' => true,
                'nearestUp' => false,
            ],
            'test outOfBoundsUp' => [
                'data' => ['A', 'B1', 'B3', 'C'],
                'searchValue' => 'C3',
                'expectedIndex' => -1,
                'nearestDown' => false,
                'nearestUp' => false,
            ],
            'test outOfBoundsUp no up' => [
                'data' => ['A', 'B1', 'B3', 'C'],
                'searchValue' => 'C3',
                'expectedIndex' => -1,
                'nearestDown' => false,
                'nearestUp' => true,
            ],
            'test outOfBoundsUp but below' => [
                'data' => ['A', 'B1', 'B3', 'C'],
                'searchValue' => 'C3',
                'expectedIndex' => 3,
                'nearestDown' => true,
                'nearestUp' => false,
            ],
            'test outOfBoundsUp but below 2' => [
                'data' => ['A', 'B1', 'B3', 'C'],
                'searchValue' => 'D3',
                'expectedIndex' => 3,
                'nearestDown' => true,
                'nearestUp' => false,
            ],
            'test outOfBoundsDown' => [
                'data' => ['B0', 'B1', 'B3', 'C'],
                'searchValue' => 'A',
                'expectedIndex' => -1,
                'nearestDown' => false,
                'nearestUp' => false,
            ],

            'test outOfBoundsDown and below' => [
                'data' => ['B0', 'B1', 'B3', 'C'],
                'searchValue' => 'A',
                'expectedIndex' => -1,
                'nearestDown' => true,
                'nearestUp' => false,
            ],
            'test outOfBoundsDown but up' => [
                'data' => ['B0', 'B1', 'B3', 'C'],
                'searchValue' => 'A',
                'expectedIndex' => 0,
                'nearestDown' => false,
                'nearestUp' => true,
            ],
        ];
    }
}