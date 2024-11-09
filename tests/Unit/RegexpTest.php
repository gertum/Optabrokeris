<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class RegexpTest extends TestCase
{

    /**
     * @dataProvider provideValuesAndRegexps
     */
    public function testRegexp(string $regexp, string $value, bool $expectedMatch ) {
        $this->assertEquals(preg_match($regexp, $value), $expectedMatch);
    }

    public static function provideValuesAndRegexps() : array {
        return [
            'test1' => [
                'regexp' => '/Etat.* skai.*ius/',
                'value' => 'Etatų skaičius',
                'expectedMatch' => true,
            ]
        ];
    }

}