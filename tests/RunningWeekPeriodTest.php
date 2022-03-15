<?php

declare(strict_types=1);

namespace Brainbits\PeriodTest;

use Brainbits\Period\RunningWeekPeriod;
use Brainbits\Period\WeekPeriod;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

use function Safe\date;

/**
 * @covers \Brainbits\Period\RunningWeekPeriod
 */
final class RunningWeekPeriodTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $period = new RunningWeekPeriod();
        $this->assertEquals(new DateTimeImmutable('last monday midnight'), $period->getStartDate());
    }

    public function testItIsInitializableWithCurrentPeriod(): void
    {
        $this->assertInstanceOf(RunningWeekPeriod::class, RunningWeekPeriod::createCurrent());
    }

    public function testItHasAStartDate(): void
    {
        $period = new RunningWeekPeriod();
        $this->assertEquals(new DateTimeImmutable('last monday midnight'), $period->getStartDate());
    }

    public function testItHasAnEndDate(): void
    {
        $period = new RunningWeekPeriod();
        $date = new DateTimeImmutable(date('Y-m-d 23:59:59'));
        $this->assertEquals($date->format('Y-m-d H:i:s'), $period->getEndDate()->format('Y-m-d H:i:s'));
    }

    public function testItHasAPeriodIdentifier(): void
    {
        $period = new RunningWeekPeriod();
        $this->assertSame(date('Y-W'), $period->getPeriod());
    }

    public function testItContainsDate(): void
    {
        $this->assertTrue(RunningWeekPeriod::createCurrent()->contains(new DateTimeImmutable('-1 min')));
    }

    public function testItDoesNotContainDate(): void
    {
        $this->assertFalse(RunningWeekPeriod::createCurrent()->contains(new DateTimeImmutable('+5 days')));
    }

    public function testItCanBeTheCurrentDate(): void
    {
        $this->assertTrue(RunningWeekPeriod::createCurrent()->isCurrent());
    }

    public function testItHasNextPeriod(): void
    {
        $period = new RunningWeekPeriod();
        $this->assertInstanceOf(WeekPeriod::class, $period->next());
        $this->assertEquals(WeekPeriod::createCurrent()->next(), $period->next());
    }

    public function testItHasPreviousPeriod(): void
    {
        $period = new RunningWeekPeriod();
        $this->assertInstanceOf(WeekPeriod::class, $period->prev());
        $this->assertEquals(WeekPeriod::createCurrent()->prev(), $period->prev());
    }

    public function testItHasNowPeriod(): void
    {
        $period = new RunningWeekPeriod();
        $this->assertInstanceOf(RunningWeekPeriod::class, $period->now());
        $this->assertEquals(RunningWeekPeriod::createCurrent(), $period->now());
    }

    public function testItHasDatePeriod(): void
    {
        $period = new RunningWeekPeriod();
        $this->assertInstanceOf(DatePeriod::class, $period->getDatePeriod(new DateInterval('P1D')));
        $i = 0;
        foreach ($period->getDatePeriod(new DateInterval('P1D')) as $x) {
            ++$i;
        }

        $this->assertSame((int) date('N'), $i);
    }

    public function testItHasDateInterval(): void
    {
        $period = new RunningWeekPeriod();
        $this->assertInstanceOf(DateInterval::class, $period->getDateInterval());
        $this->assertEquals(
            (new DateInterval('P1W'))->format('y%y_m%m_d%d_h%h_i%i_s%s'),
            $period->getDateInterval()->format('y%y_m%m_d%d_h%h_i%i_s%s')
        );
    }

    public function testItHasThisDayTranslationKey(): void
    {
        $period = new RunningWeekPeriod();
        $this->assertSame('period.week.this', $period->getTranslationKey());
    }
}
