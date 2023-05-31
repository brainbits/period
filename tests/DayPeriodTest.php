<?php

declare(strict_types=1);

namespace Brainbits\PeriodTest;

use Brainbits\Period\DayPeriod;
use Brainbits\Period\Exception\InvalidDateString;
use Brainbits\Period\Exception\InvalidPeriodIdentifier;
use Brainbits\Period\Exception\InvalidPeriodString;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/** @covers \Brainbits\Period\DayPeriod */
final class DayPeriodTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $date = new DateTimeImmutable('2015-02-03');
        $period = new DayPeriod($date);

        self::assertEquals('2015-02-03T00:00:00+01:00', $period->getStartDate()->format('c'));
    }

    public function testItIsInitializableWithPeriodIdentifier(): void
    {
        $period = DayPeriod::createFromPeriodIdentifier('day#2015-02-03');

        self::assertEquals('2015-02-03T00:00:00+01:00', $period->getStartDate()->format('c'));
    }

    public function testItIsNotInitializableWithInvalidPeriodIdentifier(): void
    {
        $this->expectException(InvalidPeriodIdentifier::class);
        $this->expectExceptionMessage('foo#bar is not a valid period identifier (e.g. day#2017-12-24).');

        DayPeriod::createFromPeriodIdentifier('foo#bar');
    }

    public function testItIsInitializableWithPeriod(): void
    {
        $period = DayPeriod::createFromPeriodString('2015-02-03');

        self::assertEquals('2015-02-03T00:00:00+01:00', $period->getStartDate()->format('c'));
    }

    public function testItIsNotInitializableWithInvalidPeriod(): void
    {
        $this->expectException(InvalidPeriodString::class);
        $this->expectExceptionMessage('2015 is not a valid day period string (e.g. 2017-12-24).');

        DayPeriod::createFromPeriodString('2015');
    }

    public function testItIsInitializableWithDate(): void
    {
        $period = DayPeriod::createFromDateString('2015-06-05');

        self::assertEquals('2015-06-05T00:00:00+02:00', $period->getStartDate()->format('c'));
    }

    public function testItIsNotInitializableWithInvalidDate(): void
    {
        $this->expectException(InvalidDateString::class);
        $this->expectExceptionMessage('2015.01.08 is not a valid day period string (e.g. 2017-12-24).');

        DayPeriod::createFromDateString('2015.01.08');
    }

    public function testItHasAStartDate(): void
    {
        $date = new DateTimeImmutable('2015-02-03 00:00:00');
        $period = new DayPeriod($date);

        self::assertEquals('2015-02-03T00:00:00+01:00', $period->getStartDate()->format('c'));
    }

    public function testItHasAnEndDate(): void
    {
        $date = new DateTimeImmutable('2015-02-03');
        $period = new DayPeriod($date);

        self::assertEquals('2015-02-03T23:59:59+01:00', $period->getEndDate()->format('c'));
    }

    public function testItHasAPeriodString(): void
    {
        $date = new DateTimeImmutable('2015-02-03');
        $period = new DayPeriod($date);

        self::assertSame('2015-02-03', $period->getPeriodString());
    }

    public function testItHasAPeriodIdentifier(): void
    {
        $date = new DateTimeImmutable('2015-02-03');
        $period = new DayPeriod($date);

        self::assertSame('day#2015-02-03', $period->getPeriodIdentifier());
    }

    public function testItContainsDate(): void
    {
        self::assertTrue(DayPeriod::createFromDateString('2015-10-05')->contains(new DateTimeImmutable('2015-10-05')));
        self::assertFalse(DayPeriod::createFromDateString('2015-10-05')->contains(new DateTimeImmutable('2015-10-03')));
        self::assertFalse(DayPeriod::createFromDateString('2015-10-05')->contains(new DateTimeImmutable('2015-10-07')));
    }

    public function testItHasDatePeriod(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new DayPeriod($date);

        self::assertInstanceOf(DatePeriod::class, $period->getDatePeriod(new DateInterval('P1D')));
        $i = 0;
        foreach ($period->getDatePeriod(new DateInterval('P1D')) as $x) {
            ++$i;
        }

        self::assertSame(1, $i);
    }

    public function testItHasDateInterval(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new DayPeriod($date);

        self::assertInstanceOf(DateInterval::class, $period->getDateInterval());
        self::assertEquals(
            (new DateInterval('P1D'))->format('y%y_m%m_d%d_h%h_i%i_s%s'),
            $period->getDateInterval()->format('y%y_m%m_d%d_h%h_i%i_s%s'),
        );
    }
}
