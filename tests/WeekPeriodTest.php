<?php

declare(strict_types = 1);

namespace Brainbits\PeriodTest;

use Brainbits\Period\Exception\PeriodException;
use Brainbits\Period\WeekPeriod;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Brainbits\Period\WeekPeriod
 */
class WeekPeriodTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new WeekPeriod($date);
        $this->assertEquals(new DateTimeImmutable('2015-01-05'), $period->getStartDate());
    }

    public function testItIsInitializableWithPeriod(): void
    {
        $period = WeekPeriod::createFromPeriodString('2015-02');
        $this->assertEquals(new DateTimeImmutable('2015-01-05'), $period->getStartDate());
    }

    public function testItIsNotInitializableWithInvalidPeriod(): void
    {
        $this->expectException(PeriodException::class);
        WeekPeriod::createFromPeriodString('2015');
    }

    public function testItIsInitializableWithDate(): void
    {
        $period = WeekPeriod::createFromDateString('2015-01-08');
        $this->assertEquals(new DateTimeImmutable('2015-01-05'), $period->getStartDate());
    }

    public function testItIsNotInitializableWithInvalidDate(): void
    {
        $this->expectException(PeriodException::class);
        WeekPeriod::createFromDateString('2015.01.08');
    }

    public function testItIsInitializableWithCurrentPeriod(): void
    {
        $this->assertInstanceOf(WeekPeriod::class, WeekPeriod::createCurrent());
    }

    public function testItHasAStartDate(): void
    {
        $period = WeekPeriod::createFromPeriodString('2015-02');
        $this->assertEquals(new DateTimeImmutable('2015-01-05 00:00:00'), $period->getStartDate());
    }

    public function testItHasAnEndDate(): void
    {
        $date = new DateTimeImmutable('2015-01-05');
        $period = new WeekPeriod($date);
        $this->assertEquals($date->modify('+1 week midnight -1 second'), $period->getEndDate());
    }

    public function testItHasAPeriodIdentifier(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new WeekPeriod($date);
        $this->assertSame('2015-02', $period->getPeriod());
    }

    public function testItContainsDate(): void
    {
        $this->assertTrue(WeekPeriod::createCurrent()->contains(new DateTimeImmutable()));
    }

    public function testItDoesNotContainDate(): void
    {
        $this->assertFalse(WeekPeriod::createCurrent()->contains(new DateTimeImmutable('-2 weeks')));
    }

    public function testItCanBeTheCurrentDate(): void
    {
        $this->assertTrue(WeekPeriod::createCurrent()->isCurrent());
    }

    public function testItCanNotBeTheCurrentDate(): void
    {
        $date = new DateTimeImmutable('2015-01-1');
        $period = new WeekPeriod($date);
        $this->assertFalse($period->isCurrent());
    }

    public function testItHasNextPeriod(): void
    {
        $period = WeekPeriod::createFromPeriodString('2015-02');
        $this->assertInstanceOf(WeekPeriod::class, $period->next());
        $this->assertEquals(WeekPeriod::createFromPeriodString('2015-03'), $period->next());
    }

    public function testItHasPreviousPeriod(): void
    {
        $period = WeekPeriod::createFromPeriodString('2015-02');
        $this->assertInstanceOf(WeekPeriod::class, $period->prev());
        $this->assertEquals(WeekPeriod::createFromPeriodString('2015-01'), $period->prev());
    }

    public function testItHasNowPeriod(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new WeekPeriod($date);
        $this->assertInstanceOf(WeekPeriod::class, $period->now());
        $this->assertEquals(WeekPeriod::createFromPeriodString(date('Y-W')), $period->now());
    }

    public function testItHasDatePeriod(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new WeekPeriod($date);
        $this->assertInstanceOf(DatePeriod::class, $period->getDatePeriod(new DateInterval('P1D')));
        $i = 0;
        foreach ($period->getDatePeriod(new DateInterval('P1D')) as $x) {
            ++$i;
        }
        $this->assertSame(7, $i);
    }

    public function testItHasDateInterval(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new WeekPeriod($date);
        $this->assertInstanceOf(DateInterval::class, $period->getDateInterval());
        $this->assertEquals((new DateInterval('P1W'))->format('%y%m%d%h%i%s'), $period->getDateInterval()->format('%y%m%d%h%i%s'));
    }

    public function testItHasPeriodDayTranslationKey(): void
    {
        $period = WeekPeriod::createFromDateString('2015-01-07');
        $this->assertSame('period.week.period', $period->getTranslationKey());
    }

    public function testItHasThisDayTranslationKey(): void
    {
        $period = new WeekPeriod(new DateTimeImmutable());
        $this->assertSame('period.week.this', $period->getTranslationKey());
    }

    public function testItHasNextDayTranslationKey(): void
    {
        $period = new WeekPeriod(new DateTimeImmutable('+1 week'));
        $this->assertSame('period.week.next', $period->getTranslationKey());
    }

    public function testItHasPrevDayTranslationKey(): void
    {
        $period = new WeekPeriod(new DateTimeImmutable('-1 week'));
        $this->assertSame('period.week.prev', $period->getTranslationKey());
    }
}
