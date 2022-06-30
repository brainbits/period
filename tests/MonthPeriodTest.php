<?php

declare(strict_types=1);

namespace Brainbits\PeriodTest;

use Brainbits\Period\Exception\InvalidDateString;
use Brainbits\Period\Exception\InvalidPeriodIdentifier;
use Brainbits\Period\Exception\InvalidPeriodString;
use Brainbits\Period\MonthPeriod;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Brainbits\Period\MonthPeriod
 */
final class MonthPeriodTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $date = new DateTimeImmutable('2015-06-03');
        $period = new MonthPeriod($date);

        self::assertEquals('2015-06-01T00:00:00+02:00', $period->getStartDate()->format('c'));
    }

    public function testItIsInitializableWithPeriodIdentifier(): void
    {
        $period = MonthPeriod::createFromPeriodIdentifier('month#2015-02');

        self::assertEquals('2015-02-01T00:00:00+01:00', $period->getStartDate()->format('c'));
    }

    public function testItIsNotInitializableWithInvalidPeriodIdentifier(): void
    {
        $this->expectException(InvalidPeriodIdentifier::class);
        $this->expectExceptionMessage('foo#bar is not a valid month period identifier (e.g. month#2017-12).');

        MonthPeriod::createFromPeriodIdentifier('foo#bar');
    }

    public function testItIsInitializableWithPeriod(): void
    {
        $period = MonthPeriod::createFromPeriodString('2015-06');

        self::assertEquals('2015-06-01T00:00:00+02:00', $period->getStartDate()->format('c'));
    }

    public function testItIsNotInitializableWithInvalidPeriod(): void
    {
        $this->expectException(InvalidPeriodString::class);
        $this->expectExceptionMessage('2015 is not a valid month period string (e.g. 2017-12).');

        MonthPeriod::createFromPeriodString('2015');
    }

    public function testItIsInitializableWithDate(): void
    {
        $period = MonthPeriod::createFromDateString('2015-06-05');

        self::assertEquals('2015-06-01T00:00:00+02:00', $period->getStartDate()->format('c'));
    }

    public function testItIsNotInitializableWithInvalidDate(): void
    {
        $this->expectException(InvalidDateString::class);
        $this->expectExceptionMessage('2015.01.08 is not a valid day period string (e.g. 2017-12-24).');

        MonthPeriod::createFromDateString('2015.01.08');
    }

    public function testItHasAStartDate(): void
    {
        $period = MonthPeriod::createFromPeriodString('2015-06');
        self::assertEquals('2015-06-01T00:00:00+02:00', $period->getStartDate()->format('c'));
    }

    public function testItHasAnEndDate(): void
    {
        $date = new DateTimeImmutable('2015-06-01');
        $period = new MonthPeriod($date);

        self::assertEquals('2015-06-30T23:59:59+02:00', $period->getEndDate()->format('c'));
    }

    public function testItHasAPeriodString(): void
    {
        $date = new DateTimeImmutable('2015-06-03');
        $period = new MonthPeriod($date);

        self::assertSame('2015-06', $period->getPeriodString());
    }

    public function testItHasAPeriodIdentifier(): void
    {
        $date = new DateTimeImmutable('2015-06-03');
        $period = new MonthPeriod($date);

        self::assertSame('month#2015-06', $period->getPeriodIdentifier());
    }

    public function testItContainsDate(): void
    {
        $period = MonthPeriod::createFromPeriodString('2022-10');

        self::assertFalse($period->contains(new DateTimeImmutable('2022-09-30')));
        self::assertTrue($period->contains(new DateTimeImmutable('2022-10-01')));
        self::assertTrue($period->contains(new DateTimeImmutable('2022-10-15')));
        self::assertTrue($period->contains(new DateTimeImmutable('2022-10-31')));
        self::assertFalse($period->contains(new DateTimeImmutable('2022-11-01')));
    }

    public function testItHasDatePeriod(): void
    {
        $date = new DateTimeImmutable('2015-02-07');
        $period = new MonthPeriod($date);

        self::assertInstanceOf(DatePeriod::class, $period->getDatePeriod(new DateInterval('P1D')));
        $i = 0;
        foreach ($period->getDatePeriod(new DateInterval('P1D')) as $x) {
            ++$i;
        }

        self::assertSame(28, $i);
    }

    public function testItHasDateInterval(): void
    {
        $date = new DateTimeImmutable();
        $period = new MonthPeriod($date);

        self::assertInstanceOf(DateInterval::class, $period->getDateInterval());
        self::assertEquals(
            (new DateInterval('P1M'))->format('y%y_m%m_d%d_h%h_i%i_s%s'),
            $period->getDateInterval()->format('y%y_m%m_d%d_h%h_i%i_s%s')
        );
    }
}
