<?php

declare(strict_types=1);

namespace Brainbits\PeriodTest;

use Brainbits\Period\Exception\InvalidPeriodIdentifier;
use Brainbits\Period\Exception\InvalidPeriodString;
use Brainbits\Period\RunningYearPeriod;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RunningYearPeriod::class)]
final class RunningYearPeriodTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $period = new RunningYearPeriod(new DateTimeImmutable('2015-06-09'), new DateTimeImmutable('2015-06-18'));

        self::assertEquals('2015-01-01T00:00:00+01:00', $period->getStartDate()->format('c'));
    }

    public function testItIsInitializableWithPeriodIdentifier(): void
    {
        $period = RunningYearPeriod::createFromPeriodIdentifier(
            'running-year#2015',
            new DateTimeImmutable('2015-06-18'),
        );

        self::assertEquals('2015-01-01T00:00:00+01:00', $period->getStartDate()->format('c'));
    }

    public function testItIsNotInitializableWithInvalidPeriodIdentifier(): void
    {
        $this->expectException(InvalidPeriodIdentifier::class);
        $this->expectExceptionMessage('foo#bar is not a valid running year period identifier (e.g. running-year#2017');

        RunningYearPeriod::createFromPeriodIdentifier('foo#bar', new DateTimeImmutable('2015-06-18'));
    }

    public function testItIsInitializableWithPeriod(): void
    {
        $period = RunningYearPeriod::createFromPeriodString('2015', new DateTimeImmutable('2015-06-18'));

        self::assertEquals('2015-01-01T00:00:00+01:00', $period->getStartDate()->format('c'));
    }

    public function testItIsNotInitializableWithInvalidPeriod(): void
    {
        $this->expectException(InvalidPeriodString::class);
        $this->expectExceptionMessage('2015-10 is not a valid year period string (e.g. 2017).');

        RunningYearPeriod::createFromPeriodString('2015-10', new DateTimeImmutable('2015-06-18'));
    }

    public function testItHasAStartDate(): void
    {
        $period = new RunningYearPeriod(new DateTimeImmutable('2015-06-02'), new DateTimeImmutable('2015-06-18'));

        self::assertEquals(
            '2015-01-01T00:00:00+01:00',
            $period->getStartDate()->format('c'),
        );
    }

    public function testItHasARunningEndDate(): void
    {
        $period = new RunningYearPeriod(new DateTimeImmutable('2015-06-02'), new DateTimeImmutable('2015-06-18'));

        self::assertEquals(
            '2015-06-18T23:59:59+02:00',
            $period->getEndDate()->format('c'),
        );
    }

    public function testItHasAnEndDate(): void
    {
        $period = new RunningYearPeriod(new DateTimeImmutable('2015-06-02'), new DateTimeImmutable('2016-06-18'));

        self::assertEquals(
            '2015-12-31T23:59:59+01:00',
            $period->getEndDate()->format('c'),
        );
    }

    public function testItHasAPeriodString(): void
    {
        $period = new RunningYearPeriod(new DateTimeImmutable('2015-06-02'), new DateTimeImmutable('2015-06-18'));

        self::assertSame('2015', $period->getPeriodString());
    }

    public function testItHasAPeriodIdentifier(): void
    {
        $period = new RunningYearPeriod(new DateTimeImmutable('2015-06-02'), new DateTimeImmutable('2015-06-18'));

        self::assertSame('running-year#2015', $period->getPeriodIdentifier());
    }

    public function testItContainsRunningDate(): void
    {
        $period = new RunningYearPeriod(new DateTimeImmutable('2015-06-02'), new DateTimeImmutable('2015-06-18'));

        self::assertFalse($period->contains(new DateTimeImmutable('2014-06-06')));
        self::assertFalse($period->contains(new DateTimeImmutable('2014-12-31')));
        self::assertTrue($period->contains(new DateTimeImmutable('2015-01-01')));
        self::assertTrue($period->contains(new DateTimeImmutable('2015-06-18')));
        self::assertFalse($period->contains(new DateTimeImmutable('2015-06-19')));
        self::assertFalse($period->contains(new DateTimeImmutable('2015-12-31')));
        self::assertFalse($period->contains(new DateTimeImmutable('2016-01-01')));
        self::assertFalse($period->contains(new DateTimeImmutable('2016-06-06')));
    }

    public function testItContainsDate(): void
    {
        $period = new RunningYearPeriod(new DateTimeImmutable('2015-06-02'), new DateTimeImmutable('2016-06-18'));

        self::assertFalse($period->contains(new DateTimeImmutable('2014-06-06')));
        self::assertFalse($period->contains(new DateTimeImmutable('2014-12-31')));
        self::assertTrue($period->contains(new DateTimeImmutable('2015-01-01')));
        self::assertTrue($period->contains(new DateTimeImmutable('2015-12-31')));
        self::assertFalse($period->contains(new DateTimeImmutable('2016-01-01')));
        self::assertFalse($period->contains(new DateTimeImmutable('2016-06-06')));
    }

    public function testItHasRunningDatePeriod(): void
    {
        $period = new RunningYearPeriod(new DateTimeImmutable('2015-06-02'), new DateTimeImmutable('2015-06-18'));

        self::assertInstanceOf(DatePeriod::class, $period->getDatePeriod(new DateInterval('P1D')));
        $i = 0;
        foreach ($period->getDatePeriod(new DateInterval('P1D')) as $x) {
            ++$i;
        }

        self::assertSame(169, $i);
    }

    public function testItHasDatePeriod(): void
    {
        $period = new RunningYearPeriod(new DateTimeImmutable('2015-06-02'), new DateTimeImmutable('2016-06-18'));

        self::assertInstanceOf(DatePeriod::class, $period->getDatePeriod(new DateInterval('P1D')));
        $i = 0;
        foreach ($period->getDatePeriod(new DateInterval('P1D')) as $x) {
            ++$i;
        }

        self::assertSame(365, $i);
    }

    public function testItHasDateInterval(): void
    {
        $period = new RunningYearPeriod(new DateTimeImmutable('2015-06-02'), new DateTimeImmutable('2016-06-18'));

        self::assertInstanceOf(DateInterval::class, $period->getDateInterval());
        self::assertEquals(
            (new DateInterval('P1Y'))->format('y%y_m%m_d%d_h%h_i%i_s%s'),
            $period->getDateInterval()->format('y%y_m%m_d%d_h%h_i%i_s%s'),
        );
    }
}
