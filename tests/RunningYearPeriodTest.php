<?php

declare(strict_types=1);

namespace Brainbits\PeriodTest;

use Brainbits\Period\RunningYearPeriod;
use Brainbits\Period\YearPeriod;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

use function Safe\date;

/**
 * @covers \Brainbits\Period\RunningYearPeriod
 */
final class RunningYearPeriodTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $period = new RunningYearPeriod();
        $this->assertEquals(new DateTimeImmutable('first day of january midnight'), $period->getStartDate());
    }

    public function testItIsInitializableWithCurrentPeriod(): void
    {
        $this->assertInstanceOf(RunningYearPeriod::class, RunningYearPeriod::createCurrent());
    }

    public function testItHasAStartDate(): void
    {
        $period = new RunningYearPeriod();
        $this->assertEquals(
            new DateTimeImmutable(date('Y') . '-01-01 00:00:00'),
            $period->getStartDate(),
        );
    }

    public function testItHasAnEndDate(): void
    {
        $period = new RunningYearPeriod();
        $date = new DateTimeImmutable(date('Y-m-d 23:59:59'));
        $this->assertEquals($date->format('Y-m-d H:i:s'), $period->getEndDate()->format('Y-m-d H:i:s'));
    }

    public function testItHasAPeriodIdentifier(): void
    {
        $period = new RunningYearPeriod();
        $this->assertSame(date('Y'), $period->getPeriod());
    }

    public function testItContainsDate(): void
    {
        $this->assertTrue(RunningYearPeriod::createCurrent()->contains(new DateTimeImmutable('-1 min')));
    }

    public function testItDoesNotContainDate(): void
    {
        $this->assertFalse(RunningYearPeriod::createCurrent()->contains(new DateTimeImmutable('+5 days')));
    }

    public function testItCanBeTheCurrentDate(): void
    {
        $this->assertTrue(RunningYearPeriod::createCurrent()->isCurrent());
    }

    public function testItHasNextPeriod(): void
    {
        $period = RunningYearPeriod::createCurrent();
        $this->assertInstanceOf(YearPeriod::class, $period->next());
        $this->assertEquals(YearPeriod::createCurrent()->next(), $period->next());
    }

    public function testItHasPreviousPeriod(): void
    {
        $period = RunningYearPeriod::createCurrent();
        $this->assertInstanceOf(YearPeriod::class, $period->prev());
        $this->assertEquals(YearPeriod::createCurrent()->prev(), $period->prev());
    }

    public function testItHasNowPeriod(): void
    {
        $period = new RunningYearPeriod();
        $this->assertInstanceOf(RunningYearPeriod::class, $period->now());
        $this->assertEquals(RunningYearPeriod::createCurrent(), $period->now());
    }

    public function testItHasDatePeriod(): void
    {
        $period = new RunningYearPeriod();
        $this->assertInstanceOf(DatePeriod::class, $period->getDatePeriod(new DateInterval('P1D')));
        $i = 0;
        foreach ($period->getDatePeriod(new DateInterval('P1D')) as $x) {
            ++$i;
        }

        $this->assertSame((int) date('z') + 1, $i);
    }

    public function testItHasDateInterval(): void
    {
        $period = new RunningYearPeriod();
        $this->assertInstanceOf(DateInterval::class, $period->getDateInterval());
        $this->assertEquals(
            (new DateInterval('P1Y'))->format('y%y_m%m_d%d_h%h_i%i_s%s'),
            $period->getDateInterval()->format('y%y_m%m_d%d_h%h_i%i_s%s')
        );
    }

    public function testItHasThisDayTranslationKey(): void
    {
        $this->assertSame('period.year.this', RunningYearPeriod::createCurrent()->getTranslationKey());
    }
}
