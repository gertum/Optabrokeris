<?php

namespace Tests\Draft;

use PHPUnit\Framework\TestCase;

class RegexpMessageTest extends TestCase
{
    /**
     * @dataProvider provideRegexps
     */
    public function testRegexp($message, $regexp) {
        $this->assertTrue( true, preg_match( $regexp, $message ));
    }

    public static function provideRegexps(): array {
        return [
            'test1' => [
                '/missing sheet \[timeslotList\]/i', 'missing sheet [timeslotList]'
            ],
        ];
    }

}