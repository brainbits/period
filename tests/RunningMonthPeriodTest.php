<?php

declare(strict_types = 1);

namespace Brainbits\PeriodTest;

use Brainbits\Period\MonthPeriod;
use Brainbits\Period\RunningMonthPeriod;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Brainbits\Period\RunningMonthPeriod
 */
class RunningMonthPeriodTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $period = new RunningMonthPeriod();
        $this->assertEquals(new DateTimeImmutable('first day of this month midnight'), $period->getStartDate());
    }

    public function testItIsInitializableWithCurrentPeriod(): void
    {
        $this->assertInstanceOf(RunningMonthPeriod::class, RunningMonthPeriod::createCurrent());
    }

    public function testItHasAStartDate(): void
    {
        $period = new RunningMonthPeriod();
        $this->assertEquals(new DateTimeImmutable(date('Y-m').'-01 00:00:00'), $period->getStartDate());
    }

    public function testItHasAnEndDate(): void
    {
        $period = new RunningMonthPeriod();
        $date = new DateTimeImmutable(date('Y-m-d 23:59:59'));
        $this->assertEquals($date->format('Y-m-d H:i:s'), $period->getEndDate()->format('Y-m-d H:i:s'));
    }

    public function testItHasAPeriodIdentifier(): void
    {
        $period = new RunningMonthPeriod();
        $this->assertSame(date('Y-m'), $period->getPeriod());
    }

    public function testItContainsDate(): void
    {
        $this->assertTrue(RunningMonthPeriod::createCurrent()->contains(new DateTimeImmutable('-1 min')));
    }

    public function testItDoesNotContainDate(): void
    {
        $this->assertFalse(RunningMonthPeriod::createCurrent()->contains(new DateTimeImmutable('+5 days')));
    }

    public function testItCanBeTheCurrentDate(): void
    {
        $this->assertTrue(RunningMonthPeriod::createCurrent()->isCurrent());
    }

    public function testItHasNextPeriod(): void
    {
        $period = new RunningMonthPeriod();
        $this->assertInstanceOf(MonthPeriod::class, $period->next());
        $this->assertEquals(MonthPeriod::createCurrent()->next(), $period->next());
    }

    public function testItHasPreviousPeriod(): void
    {
        $period = new RunningMonthPeriod();
        $this->assertInstanceOf(MonthPeriod::class, $period->prev());
        $this->assertEquals(MonthPeriod::createCurrent()->prev(), $period->prev());
    }

    public function testItHasNowPeriod(): void
    {
        $period = new RunningMonthPeriod();
        $this->assertInstanceOf(RunningMonthPeriod::class, $period->now());
        $this->assertEquals(RunningMonthPeriod::createCurrent(), $period->now());
    }

    public function testItHasDatePeriod(): void
    {
        $period = new RunningMonthPeriod();
        $this->assertInstanceOf(DatePeriod::class, $period->getDatePeriod(new DateInterval('P1D')));
        $i = 0;
        foreach ($period->getDatePeriod(new DateInterval('P1D')) as $x) {
            ++$i;
        }
        $this->assertSame((int) date('d'), $i);
    }

    public function testItHasDateInterval(): void
    {
        $period = new RunningMonthPeriod();
        $this->assertInstanceOf(DateInterval::class, $period->getDateInterval());
        $this->assertEquals((new DateInterval('P1M'))->format('y%y_m%m_d%d_h%h_i%i_s%s'), $period->getDateInterval()->format('y%y_m%m_d%d_h%h_i%i_s%s'));
    }

    public function testItHasThisDayTranslationKey(): void
    {
        $period = new RunningMonthPeriod();
        $this->assertSame('period.month.this', $period->getTranslationKey());
    }
}
