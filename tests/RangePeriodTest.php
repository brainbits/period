<?php

declare(strict_types=1);

namespace Brainbits\PeriodTest;

use Brainbits\Period\RangePeriod;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Brainbits\Period\RangePeriod
 */
class RangePeriodTest extends TestCase
{
    private RangePeriod $period;

    protected function setUp(): void
    {
        $date1 = new DateTimeImmutable('2015-06-02');
        $date2 = new DateTimeImmutable('2015-06-18');
        $this->period = new RangePeriod($date1, $date2);
    }

    public function testItIsInitializableWithDatetime(): void
    {
        $this->assertEquals(new DateTimeImmutable('2015-06-02'), $this->period->getStartDate());
        $this->assertEquals(new DateTimeImmutable('2015-06-18'), $this->period->getEndDate());
    }

    public function testItHasAStartDate(): void
    {
        $this->assertEquals(new DateTimeImmutable('2015-06-02'), $this->period->getStartDate());
    }

    public function testItHasAnEndDate(): void
    {
        $this->assertEquals(new DateTimeImmutable('2015-06-18'), $this->period->getEndDate());
    }

    public function testItHasAPeriodIdentifier(): void
    {
        $this->assertSame('2015-06-02 - 2015-06-18', $this->period->getPeriod());
    }

    public function testItContainsDate(): void
    {
        $this->assertTrue($this->period->contains(new DateTimeImmutable('2015-06-10')));
    }

    public function testItDoesNotContainDate(): void
    {
        $this->assertFalse($this->period->contains(new DateTimeImmutable('2016-05-20')));
    }

    public function testItCanBeTheCurrentDate(): void
    {
        $date1 = new DateTimeImmutable('-2 days');
        $date2 = new DateTimeImmutable('+2 days');
        $period = new RangePeriod($date1, $date2);
        $this->assertTrue($period->isCurrent());
    }

    public function testItCanNotBeTheCurrentDate(): void
    {
        $this->assertFalse($this->period->isCurrent());
    }

    public function testItHasNextPeriod(): void
    {
        $date1 = new DateTimeImmutable('2015-01-01');
        $date2 = new DateTimeImmutable('2015-01-11');
        $period = new RangePeriod($date1, $date2);
        $this->assertInstanceOf(RangePeriod::class, $period->next());
        $date3 = new DateTimeImmutable('2015-01-11');
        $date4 = new DateTimeImmutable('2015-01-21');
        $this->assertEquals(new RangePeriod($date3, $date4), $period->next());
    }

    public function testItHasPreviousPeriod(): void
    {
        $date1 = new DateTimeImmutable('2015-01-21');
        $date2 = new DateTimeImmutable('2015-01-31');
        $period = new RangePeriod($date1, $date2);
        $this->assertInstanceOf(RangePeriod::class, $period->prev());
        $date3 = new DateTimeImmutable('2015-01-11');
        $date4 = new DateTimeImmutable('2015-01-21');
        $this->assertEquals(new RangePeriod($date3, $date4), $period->prev());
    }

    public function testItHasNowPeriod(): void
    {
        $date1 = new DateTimeImmutable('2015-01-21');
        $date2 = new DateTimeImmutable('2015-01-31');
        $period = new RangePeriod($date1, $date2);
        $date3 = new DateTimeImmutable();
        $date4 = $date3->modify('+10 days');
        $this->assertInstanceOf(RangePeriod::class, $period->now());
        $this->assertEquals((new RangePeriod($date3, $date4))->getPeriod(), $period->now()->getPeriod());
    }

    public function testItHasDatePeriod(): void
    {
        $date1 = new DateTimeImmutable('2015-01-01');
        $date2 = new DateTimeImmutable('2015-01-11');
        $period = new RangePeriod($date1, $date2);
        $this->assertInstanceOf(DatePeriod::class, $period->getDatePeriod(new DateInterval('P10D')));
        $i = 0;
        foreach ($period->getDatePeriod(new DateInterval('P1D')) as $x) {
            ++$i;
        }

        $this->assertSame(10, $i);
    }

    public function testItHasDateInterval(): void
    {
        $date1 = new DateTimeImmutable('2015-01-01');
        $date2 = new DateTimeImmutable('2015-01-11');
        $period = new RangePeriod($date1, $date2);
        $this->assertInstanceOf(DateInterval::class, $period->getDateInterval());
        $this->assertEquals(
            (new DateInterval('P10D'))->format('y%y_m%m_d%d_h%h_i%i_s%s'),
            $period->getDateInterval()->format('y%y_m%m_d%d_h%h_i%i_s%s')
        );
    }

    public function testItHasPeriodRangeTranslationKey(): void
    {
        $date1 = new DateTimeImmutable('2015-01-01');
        $date2 = new DateTimeImmutable('2015-01-11');
        $period = new RangePeriod($date1, $date2);
        $this->assertSame('period.range.period', $period->getTranslationKey());
    }
}
