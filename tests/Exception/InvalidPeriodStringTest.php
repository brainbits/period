<?php

declare(strict_types=1);

namespace Brainbits\PeriodTest\Exception;

use Brainbits\Period\Exception\InvalidPeriodString;
use PHPUnit\Framework\TestCase;

final class InvalidPeriodStringTest extends TestCase
{
    public function testInvalidDayPeriod(): void
    {
        $this->assertSame(
            'foo-bar is not a valid day period string (e.g. 2017-12-24).',
            InvalidPeriodString::invalidDayPeriod('foo-bar')->getMessage(),
        );
    }

    public function testInvalidWeekPeriod(): void
    {
        $this->assertSame(
            'foo-bar is not a valid week period string (e.g. 2017-36).',
            InvalidPeriodString::invalidWeekPeriod('foo-bar')->getMessage(),
        );
    }

    public function testInvalidMonthPeriod(): void
    {
        $this->assertSame(
            'foo-bar is not a valid month period string (e.g. 2017-12).',
            InvalidPeriodString::invalidMonthPeriod('foo-bar')->getMessage(),
        );
    }

    public function testInvalidYearPeriod(): void
    {
        $this->assertSame(
            'foo-bar is not a valid year period string (e.g. 2017).',
            InvalidPeriodString::invalidYearPeriod('foo-bar')->getMessage(),
        );
    }
}
