<?php

namespace Tests\Unit\Roster;

use App\Domain\Roster\Profile;
use PHPUnit\Framework\TestCase;

class UnpackProfileTest extends TestCase
{
    /**
     * @dataProvider provideJson
     */
    public function testUnpack(string $json, Profile $expectedProfile) {
        $data = json_decode($json, true);
        $profile = new Profile($data);

        $this->assertEquals($expectedProfile, $profile);
    }

    public static function provideJson() : array {
        return [
            'test1' => [
                'json' => '{"shiftBounds":[8,20],"writeType":"original_file"}',
                'profile' => (new Profile())->setShiftBounds([8,20]),
            ]
        ];
    }
}