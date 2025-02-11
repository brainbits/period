<?php

declare(strict_types=1);

namespace Brainbits\PeriodTest;

use Brainbits\Period\Exception\InvalidDateString;
use Brainbits\Period\Exception\InvalidPeriodIdentifier;
use Brainbits\Period\Exception\InvalidPeriodString;
use Brainbits\Period\WeekPeriod;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(WeekPeriod::class)]
final class WeekPeriodTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new WeekPeriod($date);

        self::assertEquals('2015-01-05T00:00:00+01:00', $period->getStartDate()->format('c'));
    }

    public function testItIsInitializableWithPeriodIdentifier(): void
    {
        $period = WeekPeriod::createFromPeriodIdentifier('week#2015-02');

        self::assertEquals('2015-01-05T00:00:00+01:00', $period->getStartDate()->format('c'));
    }

    public function testItIsNotInitializableWithInvalidPeriodIdentifier(): void
    {
        $this->expectException(InvalidPeriodIdentifier::class);
        $this->expectExceptionMessage('foo#bar is not a valid week period identifier (e.g. week#2017-36).');

        WeekPeriod::createFromPeriodIdentifier('foo#bar');
    }

    public function testItIsInitializableWithPeriod(): void
    {
        $period = WeekPeriod::createFromPeriodString('2015-02');

        self::assertEquals('2015-01-05T00:00:00+01:00', $period->getStartDate()->format('c'));
    }

    public function testItIsNotInitializableWithInvalidPeriod(): void
    {
        $this->expectException(InvalidPeriodString::class);
        $this->expectExceptionMessage('2015 is not a valid week period string (e.g. 2017-36).');

        WeekPeriod::createFromPeriodString('2015');
    }

    public function testItIsInitializableWithDate(): void
    {
        $period = WeekPeriod::createFromDateString('2015-01-08');

        self::assertEquals('2015-01-05T00:00:00+01:00', $period->getStartDate()->format('c'));
    }

    public function testItIsNotInitializableWithInvalidDate(): void
    {
        $this->expectException(InvalidDateString::class);
        $this->expectExceptionMessage('2015.01.08 is not a valid day period string (e.g. 2017-12-24).');

        WeekPeriod::createFromDateString('2015.01.08');
    }

    public function testItHasAStartDate(): void
    {
        $period = WeekPeriod::createFromPeriodString('2015-02');

        self::assertEquals('2015-01-05T00:00:00+01:00', $period->getStartDate()->format('c'));
    }

    public function testItHasAnEndDate(): void
    {
        $date = new DateTimeImmutable('2015-01-05');
        $period = new WeekPeriod($date);

        self::assertEquals('2015-01-11T23:59:59+01:00', $period->getEndDate()->format('c'));
    }

    public function testItHasAPeriodString(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new WeekPeriod($date);

        self::assertSame('2015-02', $period->getPeriodString());
    }

    public function testItHasAPeriodIdentifier(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new WeekPeriod($date);

        self::assertSame('week#2015-02', $period->getPeriodIdentifier());
    }

    public function testItContainsDate(): void
    {
        $period = WeekPeriod::createFromPeriodString('2015-24');

        self::assertFalse($period->contains(new DateTimeImmutable('2015-05-31')));
        self::assertFalse($period->contains(new DateTimeImmutable('2015-06-07')));
        self::assertTrue($period->contains(new DateTimeImmutable('2015-06-08')));
        self::assertTrue($period->contains(new DateTimeImmutable('2015-06-14')));
        self::assertFalse($period->contains(new DateTimeImmutable('2015-06-15')));
        self::assertFalse($period->contains(new DateTimeImmutable('2015-07-01')));
    }

    public function testItHasDatePeriod(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new WeekPeriod($date);

        self::assertInstanceOf(DatePeriod::class, $period->getDatePeriod(new DateInterval('P1D')));
        $i = 0;
        foreach ($period->getDatePeriod(new DateInterval('P1D')) as $x) {
            ++$i;
        }

        self::assertSame(7, $i);
    }

    public function testItHasDateInterval(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new WeekPeriod($date);

        self::assertInstanceOf(DateInterval::class, $period->getDateInterval());
        self::assertEquals(
            (new DateInterval('P1W'))->format('y%y_m%m_d%d_h%h_i%i_s%s'),
            $period->getDateInterval()->format('y%y_m%m_d%d_h%h_i%i_s%s'),
        );
    }
}
