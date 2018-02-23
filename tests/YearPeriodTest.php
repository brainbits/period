<?php

declare(strict_types = 1);

namespace Brainbits\PeriodTest;

use Brainbits\Period\Exception\PeriodException;
use Brainbits\Period\YearPeriod;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Brainbits\Period\YearPeriod
 */
class YearPeriodTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new YearPeriod($date);
        $this->assertEquals(new DateTimeImmutable('2015-01-01'), $period->getStartDate());
    }

    public function testItIsInitializableWithPeriod(): void
    {
        $period = YearPeriod::createFromPeriodString('2015');
        $this->assertEquals(new DateTimeImmutable('2015-01-01'), $period->getStartDate());
    }

    public function testItIsNotInitializableWithInvalidPeriod(): void
    {
        $this->expectException(PeriodException::class);

        YearPeriod::createFromPeriodString('15');
    }

    public function testItIsInitializableWithDate(): void
    {
        $period = YearPeriod::createFromDateString('2015-01-08');
        $this->assertEquals(new DateTimeImmutable('2015-01-01'), $period->getStartDate());
    }

    public function testItIsNotInitializableWithInvalidDate(): void
    {
        $this->expectException(PeriodException::class);

        YearPeriod::createFromDateString('2015.01.08');
    }

    public function testItIsInitializableWithYear(): void
    {
        $period = YearPeriod::createFromYear(2015);
        $this->assertEquals(new DateTimeImmutable('2015-01-01'), $period->getStartDate());
    }

    public function testItIsInitializableWithCurrentPeriod(): void
    {
        $this->assertInstanceOf(YearPeriod::class, YearPeriod::createCurrent());
    }

    public function testItHasAStartDate(): void
    {
        $period = YearPeriod::createFromPeriodString('2015');
        $this->assertEquals(new DateTimeImmutable('2015-01-01 00:00:00'), $period->getStartDate());
    }

    public function testItHasAnEndDate(): void
    {
        $date = new DateTimeImmutable('2015-01-01');
        $period = new YearPeriod($date);
        $this->assertEquals($date->modify('+1 year midnight -1 second'), $period->getEndDate());
    }

    public function testItHasAPeriodIdentifier(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new YearPeriod($date);
        $this->assertSame('2015', $period->getPeriod());
    }

    public function testItContainsDate(): void
    {
        $this->assertTrue(YearPeriod::createCurrent()->contains(new DateTimeImmutable()));
    }

    public function testItDoesNotContainDate(): void
    {
        $this->assertFalse(YearPeriod::createCurrent()->contains(new DateTimeImmutable('-2 years')));
    }

    public function testItCanBeTheCurrentDate(): void
    {
        $this->assertTrue(YearPeriod::createCurrent()->isCurrent());
    }

    public function testItCanNotBeTheCurrentDate(): void
    {
        $period = YearPeriod::createFromPeriodString('2015');
        $this->assertFalse($period->isCurrent());
    }

    public function testItHasNextPeriod(): void
    {
        $period = YearPeriod::createFromPeriodString('2015');
        $this->assertInstanceOf(YearPeriod::class, $period->next());
        $this->assertEquals(YearPeriod::createFromPeriodString('2016'), $period->next());
    }

    public function testItHasPreviousPeriod(): void
    {
        $period = YearPeriod::createFromPeriodString('2015');
        $this->assertInstanceOf(YearPeriod::class, $period->prev());
        $this->assertEquals(YearPeriod::createFromPeriodString('2014'), $period->prev());
    }

    public function testItHasNowPeriod(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new YearPeriod($date);
        $this->assertInstanceOf(YearPeriod::class, $period->now());
        $this->assertEquals(YearPeriod::createFromPeriodString(date('Y')), $period->now());
    }

    public function testItHasDatePeriod(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new YearPeriod($date);
        $this->assertInstanceOf(DatePeriod::class, $period->getDatePeriod(new DateInterval('P1D')));
        $i = 0;
        foreach ($period->getDatePeriod(new DateInterval('P1D')) as $x) {
            ++$i;
        }
        $this->assertSame(365, $i);
    }

    public function testItHasDateInterval(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new YearPeriod($date);
        $this->assertInstanceOf(DateInterval::class, $period->getDateInterval());
        $this->assertEquals((new DateInterval('P1Y'))->format('y%y_m%m_d%d_h%h_i%i_s%s'), $period->getDateInterval()->format('y%y_m%m_d%d_h%h_i%i_s%s'));
    }

    public function testItHasPeriodDayTranslationKey(): void
    {
        $period = YearPeriod::createFromPeriodString('2015');
        $this->assertSame('period.year.period', $period->getTranslationKey());
    }

    public function testItHasThisDayTranslationKey(): void
    {
        $period = new YearPeriod(new DateTimeImmutable());
        $this->assertSame('period.year.this', $period->getTranslationKey());
    }

    public function testItHasNextDayTranslationKey(): void
    {
        $period = new YearPeriod(new DateTimeImmutable('+1 year'));
        $this->assertSame('period.year.next', $period->getTranslationKey());
    }

    public function testItHasPrevDayTranslationKey(): void
    {
        $period = new YearPeriod(new DateTimeImmutable('-1 year'));
        $this->assertSame('period.year.prev', $period->getTranslationKey());
    }
}
