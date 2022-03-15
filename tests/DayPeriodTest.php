<?php

declare(strict_types=1);

namespace Brainbits\PeriodTest;

use Brainbits\Period\DayPeriod;
use Brainbits\Period\Exception\InvalidDateString;
use Brainbits\Period\Exception\InvalidPeriodString;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

use function Safe\date;

/**
 * @covers \Brainbits\Period\DayPeriod
 */
final class DayPeriodTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $date = new DateTimeImmutable('2015-02-03');
        $period = new DayPeriod($date);
        $this->assertEquals(new DateTimeImmutable('2015-02-03'), $period->getStartDate());
    }

    public function testItIsInitializableWithPeriod(): void
    {
        $period = DayPeriod::createFromPeriodString('2015-02-03');
        $this->assertEquals(new DateTimeImmutable('2015-02-03'), $period->getStartDate());
    }

    public function testItIsNotInitializableWithInvalidPeriod(): void
    {
        $this->expectException(InvalidPeriodString::class);
        DayPeriod::createFromPeriodString('2015');
    }

    public function testItIsInitializableWithDate(): void
    {
        $period = DayPeriod::createFromDateString('2015-06-05');
        $this->assertEquals(new DateTimeImmutable('2015-06-05'), $period->getStartDate());
    }

    public function testItIsNotInitializableWithInvalidDate(): void
    {
        $this->expectException(InvalidDateString::class);
        DayPeriod::createFromDateString('2015.01.08');
    }

    public function testItIsInitializableWithCurrentPeriod(): void
    {
        $period = DayPeriod::createCurrent();
        $this->assertEquals(new DateTimeImmutable(date('Y-m-d')), $period->getStartDate());
    }

    public function testItHasAStartDate(): void
    {
        $date = new DateTimeImmutable('2015-02-03');
        $period = new DayPeriod($date);
        $this->assertEquals($date->modify('midnight'), $period->getStartDate());
    }

    public function testItHasAnEndDate(): void
    {
        $date = new DateTimeImmutable('2015-02-03');
        $period = new DayPeriod($date);
        $this->assertEquals($date->modify('+1 day midnight -1 second'), $period->getEndDate());
    }

    public function testItHasAPeriodIdentifier(): void
    {
        $date = new DateTimeImmutable('2015-02-03');
        $period = new DayPeriod($date);
        $this->assertSame('2015-02-03', $period->getPeriod());
    }

    public function testItContainsDate(): void
    {
        $this->assertTrue(DayPeriod::createCurrent()->contains(new DateTimeImmutable()));
    }

    public function testItDoesNotContainDate(): void
    {
        $this->assertFalse(DayPeriod::createCurrent()->contains(new DateTimeImmutable('-2 days')));
    }

    public function testItCanBeTheCurrentDate(): void
    {
        $this->assertTrue(DayPeriod::createCurrent()->isCurrent());
    }

    public function testItCanNotBeTheCurrentDate(): void
    {
        $date = new DateTimeImmutable('2015-01-01');
        $period = new DayPeriod($date);
        $this->assertFalse($period->isCurrent());
    }

    public function testItHasNextPeriod(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new DayPeriod($date);
        $this->assertInstanceOf(DayPeriod::class, $period->next());
        $this->assertEquals(DayPeriod::createFromPeriodString('2015-01-08'), $period->next());
    }

    public function testItHasPreviousPeriod(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new DayPeriod($date);
        $this->assertInstanceOf(DayPeriod::class, $period->prev());
        $this->assertEquals(DayPeriod::createFromPeriodString('2015-01-06'), $period->prev());
    }

    public function testItHasNowPeriod(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new DayPeriod($date);
        $this->assertInstanceOf(DayPeriod::class, $period->now());
        $this->assertEquals(DayPeriod::createFromPeriodString(date('Y-m-d')), $period->now());
    }

    public function testItHasDatePeriod(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new DayPeriod($date);
        $this->assertInstanceOf(DatePeriod::class, $period->getDatePeriod(new DateInterval('P1D')));
        $i = 0;
        foreach ($period->getDatePeriod(new DateInterval('P1D')) as $x) {
            ++$i;
        }

        $this->assertSame(1, $i);
    }

    public function testItHasDateInterval(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new DayPeriod($date);
        $this->assertInstanceOf(DateInterval::class, $period->getDateInterval());
        $this->assertEquals(
            (new DateInterval('P1D'))->format('y%y_m%m_d%d_h%h_i%i_s%s'),
            $period->getDateInterval()->format('y%y_m%m_d%d_h%h_i%i_s%s')
        );
    }

    public function testItHasPeriodDayTranslationKey(): void
    {
        $period = DayPeriod::createFromPeriodString('2015-01-07');
        $this->assertSame('period.day.period', $period->getTranslationKey());
    }

    public function testItHasThisDayTranslationKey(): void
    {
        $period = new DayPeriod(new DateTimeImmutable());
        $this->assertSame('period.day.this', $period->getTranslationKey());
    }

    public function testItHasNextDayTranslationKey(): void
    {
        $period = new DayPeriod(new DateTimeImmutable('+1 day'));
        $this->assertSame('period.day.next', $period->getTranslationKey());
    }

    public function testItHasPrevDayTranslationKey(): void
    {
        $period = new DayPeriod(new DateTimeImmutable('-1 day'));
        $this->assertSame('period.day.prev', $period->getTranslationKey());
    }
}
