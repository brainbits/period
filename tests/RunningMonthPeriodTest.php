<?php

declare(strict_types=1);

namespace Brainbits\PeriodTest;

use Brainbits\Period\Exception\InvalidPeriodIdentifier;
use Brainbits\Period\Exception\InvalidPeriodString;
use Brainbits\Period\RunningMonthPeriod;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Brainbits\Period\RunningMonthPeriod
 */
final class RunningMonthPeriodTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $period = new RunningMonthPeriod(new DateTimeImmutable('2015-06-02'), new DateTimeImmutable('2015-06-18'));

        self::assertEquals('2015-06-01T00:00:00+02:00', $period->getStartDate()->format('c'));
    }

    public function testItIsInitializableWithPeriodIdentifier(): void
    {
        $period = RunningMonthPeriod::createFromPeriodIdentifier(
            'running-month#2015-02',
            new DateTimeImmutable('2015-06-18'),
        );

        self::assertEquals('2015-02-01T00:00:00+01:00', $period->getStartDate()->format('c'));
    }

    public function testItIsNotInitializableWithInvalidPeriodIdentifier(): void
    {
        $this->expectException(InvalidPeriodIdentifier::class);
        $this->expectExceptionMessage(
            'foo#bar is not a valid running month period identifier (e.g. running-month#2017-12).'
        );

        RunningMonthPeriod::createFromPeriodIdentifier('foo#bar', new DateTimeImmutable('2015-06-18'));
    }

    public function testItIsInitializableWithPeriod(): void
    {
        $period = RunningMonthPeriod::createFromPeriodString('2015-06', new DateTimeImmutable('2015-06-18'));

        self::assertEquals('2015-06-01T00:00:00+02:00', $period->getStartDate()->format('c'));
    }

    public function testItIsNotInitializableWithInvalidPeriod(): void
    {
        $this->expectException(InvalidPeriodString::class);
        $this->expectExceptionMessage('2015 is not a valid month period string (e.g. 2017-12).');

        RunningMonthPeriod::createFromPeriodString('2015', new DateTimeImmutable('2015-06-18'));
    }

    public function testItHasAStartDate(): void
    {
        $period = new RunningMonthPeriod(new DateTimeImmutable('2015-06-02'), new DateTimeImmutable('2015-06-18'));

        self::assertEquals('2015-06-01T00:00:00+02:00', $period->getStartDate()->format('c'));
    }

    public function testItHasARunningEndDate(): void
    {
        $period = new RunningMonthPeriod(new DateTimeImmutable('2015-06-02'), new DateTimeImmutable('2015-06-18'));

        self::assertEquals('2015-06-18T23:59:59+02:00', $period->getEndDate()->format('c'));
    }

    public function testItHasAnEndDate(): void
    {
        $period = new RunningMonthPeriod(new DateTimeImmutable('2015-06-02'), new DateTimeImmutable('2015-07-03'));

        self::assertEquals('2015-06-30T23:59:59+02:00', $period->getEndDate()->format('c'));
    }

    public function testItHasAPeriodString(): void
    {
        $period = new RunningMonthPeriod(new DateTimeImmutable('2015-06-02'), new DateTimeImmutable('2015-06-18'));

        self::assertSame('2015-06', $period->getPeriodString());
    }

    public function testItHasAPeriodIdentifier(): void
    {
        $period = new RunningMonthPeriod(new DateTimeImmutable('2015-06-02'), new DateTimeImmutable('2015-06-18'));

        self::assertSame('running-month#2015-06', $period->getPeriodIdentifier());
    }

    public function testItContainsRunningDate(): void
    {
        $period = new RunningMonthPeriod(new DateTimeImmutable('2015-06-02'), new DateTimeImmutable('2015-06-18'));

        self::assertFalse($period->contains(new DateTimeImmutable('2015-05-14')));
        self::assertFalse($period->contains(new DateTimeImmutable('2015-05-31')));
        self::assertTrue($period->contains(new DateTimeImmutable('2015-06-01')));
        self::assertTrue($period->contains(new DateTimeImmutable('2015-06-18')));
        self::assertFalse($period->contains(new DateTimeImmutable('2015-06-19')));
        self::assertFalse($period->contains(new DateTimeImmutable('2015-07-25')));
    }

    public function testItContainsDate(): void
    {
        $period = new RunningMonthPeriod(new DateTimeImmutable('2015-06-02'), new DateTimeImmutable('2015-07-18'));

        self::assertFalse($period->contains(new DateTimeImmutable('2015-05-14')));
        self::assertFalse($period->contains(new DateTimeImmutable('2015-05-31')));
        self::assertTrue($period->contains(new DateTimeImmutable('2015-06-01')));
        self::assertTrue($period->contains(new DateTimeImmutable('2015-06-18')));
        self::assertTrue($period->contains(new DateTimeImmutable('2015-06-30')));
        self::assertFalse($period->contains(new DateTimeImmutable('2015-07-01')));
        self::assertFalse($period->contains(new DateTimeImmutable('2015-07-25')));
    }

    public function testItHasRunningDatePeriod(): void
    {
        $period = new RunningMonthPeriod(new DateTimeImmutable('2015-06-02'), new DateTimeImmutable('2015-06-18'));

        self::assertInstanceOf(DatePeriod::class, $period->getDatePeriod(new DateInterval('P1D')));
        $i = 0;
        foreach ($period->getDatePeriod(new DateInterval('P1D')) as $x) {
            ++$i;
        }

        self::assertSame(18, $i);
    }

    public function testItHasDatePeriod(): void
    {
        $period = new RunningMonthPeriod(new DateTimeImmutable('2015-06-02'), new DateTimeImmutable('2015-07-18'));

        self::assertInstanceOf(DatePeriod::class, $period->getDatePeriod(new DateInterval('P1D')));
        $i = 0;
        foreach ($period->getDatePeriod(new DateInterval('P1D')) as $x) {
            ++$i;
        }

        self::assertSame(30, $i);
    }

    public function testItHasDateInterval(): void
    {
        $period = new RunningMonthPeriod(new DateTimeImmutable('2015-06-02'), new DateTimeImmutable('2015-06-18'));

        self::assertInstanceOf(DateInterval::class, $period->getDateInterval());
        self::assertEquals(
            (new DateInterval('P1M'))->format('y%y_m%m_d%d_h%h_i%i_s%s'),
            $period->getDateInterval()->format('y%y_m%m_d%d_h%h_i%i_s%s')
        );
    }
}
