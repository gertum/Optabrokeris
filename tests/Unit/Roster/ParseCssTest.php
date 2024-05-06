<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Hospital\Cell;
use PHPUnit\Framework\TestCase;

class ParseCssTest extends TestCase
{
    /**
     * @dataProvider provideCssData
     */
    public function testParse (string $css, array $expectedArray ) {
        $result = Cell::parseCss($css);
        $this->assertEquals($expectedArray, $result);
    }

    public static function provideCssData() : array {
        return [
            'empty' => [
                'css' => '',
                'expectedArray' => []
            ],
            'simple' => [
                'css' => 'color: #FF0000;',
                'expectedArray' => ['color'=>'#FF0000']
            ],
        ];
    }

}