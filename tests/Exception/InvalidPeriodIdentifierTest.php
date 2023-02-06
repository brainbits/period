<?php

declare(strict_types=1);

namespace Brainbits\PeriodTest\Exception;

use Brainbits\Period\DayPeriod;
use Brainbits\Period\Exception\InvalidPeriodIdentifier;
use PHPUnit\Framework\TestCase;

final class InvalidPeriodIdentifierTest extends TestCase
{
    public function testUnknownPeriodIdentifier(): void
    {
        $this->assertSame(
            'foo-bar is not a valid period identifier.',
            InvalidPeriodIdentifier::unknownPeriodIdentifier('foo-bar')->getMessage(),
        );
    }

    public function testUnknownPeriod(): void
    {
        $this->assertSame(
            'Brainbits\Period\DayPeriod is not a valid period.',
            InvalidPeriodIdentifier::unknownPeriod(DayPeriod::createFromPeriodString('2022-02-02'))->getMessage(),
        );
    }

    public function testInvalidDayPeriodIdentifier(): void
    {
        $this->assertSame(
            'foo-bar is not a valid period identifier (e.g. day#2017-12-24).',
            InvalidPeriodIdentifier::invalidDayPeriodIdentifier('foo-bar')->getMessage(),
        );
    }

    public function testInvalidWeekPeriodIdentifier(): void
    {
        $this->assertSame(
            'foo-bar is not a valid week period identifier (e.g. week#2017-36).',
            InvalidPeriodIdentifier::invalidWeekPeriodIdentifier('foo-bar')->getMessage(),
        );
    }

    public function testInvalidMonthPeriodIdentifier(): void
    {
        $this->assertSame(
            'foo-bar is not a valid month period identifier (e.g. month#2017-12).',
            InvalidPeriodIdentifier::invalidMonthPeriodIdentifier('foo-bar')->getMessage(),
        );
    }

    public function testInvalidYearPeriodIdentifier(): void
    {
        $this->assertSame(
            'foo-bar is not a valid year period identifier (e.g. year#2017).',
            InvalidPeriodIdentifier::invalidYearPeriodIdentifier('foo-bar')->getMessage(),
        );
    }

    public function testInvalidRangePeriodIdentifier(): void
    {
        $this->assertSame(
            'foo-bar is not a valid range period identifier (e.g. range#2017-12-24#2017-12-26).',
            InvalidPeriodIdentifier::invalidRangePeriodIdentifier('foo-bar')->getMessage(),
        );
    }

    public function testInvalidRunningWeekPeriodIdentifier(): void
    {
        $this->assertSame(
            'foo-bar is not a valid running week period identifier (e.g. running-week#2017-36).',
            InvalidPeriodIdentifier::invalidRunningWeekPeriodIdentifier('foo-bar')->getMessage(),
        );
    }

    public function testInvalidRunningMonthPeriodIdentifier(): void
    {
        $this->assertSame(
            'foo-bar is not a valid running month period identifier (e.g. running-month#2017-12).',
            InvalidPeriodIdentifier::invalidRunningMonthPeriodIdentifier('foo-bar')->getMessage(),
        );
    }

    public function testInvalidRunningYearPeriodIdentifier(): void
    {
        $this->assertSame(
            'foo-bar is not a valid running year period identifier (e.g. running-year#2017).',
            InvalidPeriodIdentifier::invalidRunningYearPeriodIdentifier('foo-bar')->getMessage(),
        );
    }
}
