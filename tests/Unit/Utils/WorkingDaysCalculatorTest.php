<?php

namespace Tests\Unit\Utils;

use App\Domain\Util\HolidayProviderFactory;
use App\Util\WorkingDaysCalculator;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class WorkingDaysCalculatorTest extends TestCase
{
    /**
     * @dataProvider calculateProvider
     */
    public function testCalculate(Carbon $date, int $expectedDays ) {
        $holidayProviderFactory = new HolidayProviderFactory();
        $holidayProvider = $holidayProviderFactory->make();
        $holidayDays = WorkingDaysCalculator::calculateWorkingDaysInMonth($date->year, $date->month,  $holidayProvider, [1,2,3,4,5]);

        $this->assertEquals($expectedDays, $holidayDays);
    }

    public static function calculateProvider(): array {
        return [
            'test1' => [
                'date' => Carbon::create(2024, 12, 1),
                'expectedDays' => 19
            ],
        ];
    }

}