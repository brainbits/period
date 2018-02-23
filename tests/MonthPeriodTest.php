<?php

declare(strict_types = 1);

namespace Brainbits\PeriodTest;

use Brainbits\Period\Exception\PeriodException;
use Brainbits\Period\MonthPeriod;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Brainbits\Period\MonthPeriod
 */
class MonthPeriodTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $date = new DateTimeImmutable('2015-06-03');
        $period = new MonthPeriod($date);
        $this->assertEquals(new DateTimeImmutable('2015-06-01'), $period->getStartDate());
    }

    public function testItIsInitializableWithPeriod(): void
    {
        $period = MonthPeriod::createFromPeriodString('2015-06');
        $this->assertEquals(new DateTimeImmutable('2015-06-01'), $period->getStartDate());
    }

    public function testItIsNotInitializableWithInvalidPeriod(): void
    {
        $this->expectException(PeriodException::class);
        MonthPeriod::createFromPeriodString('2015');
    }

    public function testItIsInitializableWithDate(): void
    {
        $period = MonthPeriod::createFromDateString('2015-06-05');
        $this->assertEquals(new DateTimeImmutable('2015-06-01'), $period->getStartDate());
    }

    public function testItIsNotInitializableWithInvalidDate(): void
    {
        $this->expectException(PeriodException::class);
        MonthPeriod::createFromDateString('2015.01.08');
    }

    public function testItIsInitializableWithCurrentPeriod(): void
    {
        $this->assertInstanceOf(MonthPeriod::class, MonthPeriod::createCurrent());
    }

    public function testItHasAStartDate(): void
    {
        $period = MonthPeriod::createFromPeriodString('2015-06');
        $this->assertEquals(new DateTimeImmutable('2015-06-01 00:00:00'), $period->getStartDate());
    }

    public function testItHasAnEndDate(): void
    {
        $date = new DateTimeImmutable('2015-06-01');
        $period = new MonthPeriod($date);
        $this->assertEquals($date->modify('+1 month midnight -1 second'), $period->getEndDate());
    }

    public function testItHasAPeriodIdentifier(): void
    {
        $date = new DateTimeImmutable('2015-06-03');
        $period = new MonthPeriod($date);
        $this->assertSame('2015-06', $period->getPeriod());
    }

    public function testItContainsDate(): void
    {
        $this->assertTrue(MonthPeriod::createCurrent()->contains(new DateTimeImmutable()));
    }

    public function testItDoesNotContainDate(): void
    {
        $this->assertFalse(MonthPeriod::createCurrent()->contains(new DateTimeImmutable('-2 months')));
    }

    public function testItCanBeTheCurrentDate(): void
    {
        $this->assertTrue(MonthPeriod::createCurrent()->isCurrent());
    }

    public function testItCanNotBeTheCurrentDate(): void
    {
        $date = new DateTimeImmutable('2015-01');
        $period = new MonthPeriod($date);
        $this->assertFalse($period->isCurrent());
    }

    public function testItHasNextPeriod(): void
    {
        $date = new DateTimeImmutable('2015-06');
        $period = new MonthPeriod($date);
        $this->assertInstanceOf(MonthPeriod::class, $period->next());
        $this->assertEquals(MonthPeriod::createFromPeriodString('2015-07'), $period->next());
    }

    public function testItHasPreviousPeriod(): void
    {
        $date = new DateTimeImmutable('2015-06');
        $period = new MonthPeriod($date);
        $this->assertInstanceOf(MonthPeriod::class, $period->prev());
        $this->assertEquals(MonthPeriod::createFromPeriodString('2015-05'), $period->prev());
    }

    public function testItHasNowPeriod(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new MonthPeriod($date);
        $this->assertInstanceOf(MonthPeriod::class, $period->now());
        $this->assertEquals(MonthPeriod::createFromPeriodString(date('Y-m')), $period->now());
    }

    public function testItHasDatePeriod(): void
    {
        $date = new DateTimeImmutable('2015-02-07');
        $period = new MonthPeriod($date);
        $this->assertInstanceOf(DatePeriod::class, $period->getDatePeriod(new DateInterval('P1D')));
        $i = 0;
        foreach ($period->getDatePeriod(new DateInterval('P1D')) as $x) {
            ++$i;
        }
        $this->assertSame(28, $i);
    }

    public function testItHasDateInterval(): void
    {
        $date = new DateTimeImmutable();
        $period = new MonthPeriod($date);
        $this->assertInstanceOf(DateInterval::class, $period->getDateInterval());
        $this->assertEquals((new DateInterval('P1M'))->format('y%y_m%m_d%d_h%h_i%i_s%s'), $period->getDateInterval()->format('y%y_m%m_d%d_h%h_i%i_s%s'));
    }

    public function testItHasPeriodDayTranslationKey(): void
    {
        $period = MonthPeriod::createFromDateString('2015-01-07');
        $this->assertSame('period.month.period', $period->getTranslationKey());
    }

    public function testItHasThisDayTranslationKey(): void
    {
        $period = new MonthPeriod(new DateTimeImmutable());
        $this->assertSame('period.month.this', $period->getTranslationKey());
    }

    public function testItHasNextDayTranslationKey(): void
    {
        $period = new MonthPeriod(new DateTimeImmutable('first day of this month +1 month'));
        $this->assertSame('period.month.next', $period->getTranslationKey());
    }

    public function testItHasPrevDayTranslationKey(): void
    {
        $period = new MonthPeriod(new DateTimeImmutable('first day of this month -1 month'));
        $this->assertSame('period.month.prev', $period->getTranslationKey());
    }
}
