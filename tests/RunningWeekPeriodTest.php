<?php

declare(strict_types=1);

namespace Brainbits\PeriodTest;

use Brainbits\Period\Exception\InvalidPeriodIdentifier;
use Brainbits\Period\Exception\InvalidPeriodString;
use Brainbits\Period\RunningWeekPeriod;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RunningWeekPeriod::class)]
final class RunningWeekPeriodTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $period = new RunningWeekPeriod(new DateTimeImmutable('2015-06-09'), new DateTimeImmutable('2015-06-18'));

        self::assertEquals('2015-06-08T00:00:00+02:00', $period->getStartDate()->format('c'));
    }

    public function testItIsInitializableWithPeriodIdentifier(): void
    {
        $period = RunningWeekPeriod::createFromPeriodIdentifier(
            'running-week#2015-09',
            new DateTimeImmutable('2015-06-18'),
        );

        self::assertEquals('2015-02-23T00:00:00+01:00', $period->getStartDate()->format('c'));
    }

    public function testItIsNotInitializableWithInvalidPeriodIdentifier(): void
    {
        $this->expectException(InvalidPeriodIdentifier::class);
        $this->expectExceptionMessage(
            'foo#bar is not a valid running week period identifier (e.g. running-week#2017-36).',
        );

        RunningWeekPeriod::createFromPeriodIdentifier('foo#bar', new DateTimeImmutable('2015-06-18'));
    }

    public function testItIsInitializableWithPeriod(): void
    {
        $period = RunningWeekPeriod::createFromPeriodString('2015-23', new DateTimeImmutable('2015-06-18'));

        self::assertEquals('2015-06-01T00:00:00+02:00', $period->getStartDate()->format('c'));
    }

    public function testItIsNotInitializableWithInvalidPeriod(): void
    {
        $this->expectException(InvalidPeriodString::class);
        $this->expectExceptionMessage('2015 is not a valid week period string (e.g. 2017-36).');

        RunningWeekPeriod::createFromPeriodString('2015', new DateTimeImmutable('2015-06-18'));
    }

    public function testItHasAStartDate(): void
    {
        $period = new RunningWeekPeriod(new DateTimeImmutable('2015-06-09'), new DateTimeImmutable('2015-06-18'));

        self::assertEquals('2015-06-08T00:00:00+02:00', $period->getStartDate()->format('c'));
    }

    public function testItHasARunningEndDate(): void
    {
        $period = new RunningWeekPeriod(new DateTimeImmutable('2015-06-09'), new DateTimeImmutable('2015-06-12'));

        self::assertEquals(
            '2015-06-12T23:59:59+02:00',
            $period->getEndDate()->format('c'),
        );
    }

    public function testItHasAnEndDate(): void
    {
        $period = new RunningWeekPeriod(new DateTimeImmutable('2015-06-09'), new DateTimeImmutable('2015-06-28'));

        self::assertEquals(
            '2015-06-14T23:59:59+02:00',
            $period->getEndDate()->format('c'),
        );
    }

    public function testItHasAPeriodString(): void
    {
        $period = new RunningWeekPeriod(new DateTimeImmutable('2015-06-09'), new DateTimeImmutable('2015-06-28'));

        self::assertSame('2015-24', $period->getPeriodString());
    }

    public function testItHasAPeriodIdentifier(): void
    {
        $period = new RunningWeekPeriod(new DateTimeImmutable('2015-06-09'), new DateTimeImmutable('2015-06-28'));

        self::assertSame('running-week#2015-24', $period->getPeriodIdentifier());
    }

    public function testItContainsRunningDate(): void
    {
        $period = new RunningWeekPeriod(new DateTimeImmutable('2015-06-09'), new DateTimeImmutable('2015-06-12'));

        self::assertFalse($period->contains(new DateTimeImmutable('2015-05-04')));
        self::assertFalse($period->contains(new DateTimeImmutable('2015-06-07')));
        self::assertTrue($period->contains(new DateTimeImmutable('2015-06-08')));
        self::assertTrue($period->contains(new DateTimeImmutable('2015-06-12')));
        self::assertFalse($period->contains(new DateTimeImmutable('2015-06-13')));
        self::assertFalse($period->contains(new DateTimeImmutable('2015-07-13')));
    }

    public function testItContainsDate(): void
    {
        $period = new RunningWeekPeriod(new DateTimeImmutable('2015-06-09'), new DateTimeImmutable('2015-06-18'));

        self::assertFalse($period->contains(new DateTimeImmutable('2015-05-04')));
        self::assertFalse($period->contains(new DateTimeImmutable('2015-06-07')));
        self::assertTrue($period->contains(new DateTimeImmutable('2015-06-08')));
        self::assertTrue($period->contains(new DateTimeImmutable('2015-06-14')));
        self::assertFalse($period->contains(new DateTimeImmutable('2015-07-15')));
    }

    public function testItHasRunningDatePeriod(): void
    {
        $period = new RunningWeekPeriod(new DateTimeImmutable('2015-06-09'), new DateTimeImmutable('2015-06-12'));

        self::assertInstanceOf(DatePeriod::class, $period->getDatePeriod(new DateInterval('P1D')));
        $i = 0;
        foreach ($period->getDatePeriod(new DateInterval('P1D')) as $x) {
            ++$i;
        }

        self::assertSame(5, $i);
    }

    public function testItHasDatePeriod(): void
    {
        $period = new RunningWeekPeriod(new DateTimeImmutable('2015-06-09'), new DateTimeImmutable('2015-06-28'));

        self::assertInstanceOf(DatePeriod::class, $period->getDatePeriod(new DateInterval('P1D')));
        $i = 0;
        foreach ($period->getDatePeriod(new DateInterval('P1D')) as $x) {
            ++$i;
        }

        self::assertSame(7, $i);
    }

    public function testItHasDateInterval(): void
    {
        $period = new RunningWeekPeriod(new DateTimeImmutable('2015-06-09'), new DateTimeImmutable('2015-06-28'));

        self::assertInstanceOf(DateInterval::class, $period->getDateInterval());
        self::assertEquals(
            (new DateInterval('P1W'))->format('y%y_m%m_d%d_h%h_i%i_s%s'),
            $period->getDateInterval()->format('y%y_m%m_d%d_h%h_i%i_s%s'),
        );
    }
}
