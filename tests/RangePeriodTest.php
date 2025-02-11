<?php

declare(strict_types=1);

namespace Brainbits\PeriodTest;

use Brainbits\Period\Exception\InvalidPeriodIdentifier;
use Brainbits\Period\RangePeriod;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RangePeriod::class)]
final class RangePeriodTest extends TestCase
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
        self::assertEquals('2015-06-02T00:00:00+02:00', $this->period->getStartDate()->format('c'));
        self::assertEquals('2015-06-18T23:59:59+02:00', $this->period->getEndDate()->format('c'));
    }

    public function testItIsInitializableWithPeriodIdentifier(): void
    {
        $period = RangePeriod::createFromPeriodIdentifier('range#2015-02-03#2015-03-17');

        self::assertEquals('2015-02-03T00:00:00+01:00', $period->getStartDate()->format('c'));
        self::assertEquals('2015-03-17T23:59:59+01:00', $period->getEndDate()->format('c'));
    }

    public function testItIsNotInitializableWithInvalidPeriodIdentifier(): void
    {
        $this->expectException(InvalidPeriodIdentifier::class);
        $this->expectExceptionMessage(
            'foo#bar#baz is not a valid range period identifier (e.g. range#2017-12-24#2017-12-26).',
        );

        RangePeriod::createFromPeriodIdentifier('foo#bar#baz');
    }

    public function testItHasAStartDate(): void
    {
        self::assertEquals('2015-06-02T00:00:00+02:00', $this->period->getStartDate()->format('c'));
    }

    public function testItHasAnEndDate(): void
    {
        self::assertEquals('2015-06-18T23:59:59+02:00', $this->period->getEndDate()->format('c'));
    }

    public function testItHasAPeriodString(): void
    {
        self::assertSame('2015-06-02 - 2015-06-18', $this->period->getPeriodString());
    }

    public function testItHasAPeriodIdentifier(): void
    {
        self::assertSame('range#2015-06-02#2015-06-18', $this->period->getPeriodIdentifier());
    }

    public function testItContainsDate(): void
    {
        self::assertFalse($this->period->contains(new DateTimeImmutable('2016-05-15')));
        self::assertFalse($this->period->contains(new DateTimeImmutable('2015-06-01')));
        self::assertTrue($this->period->contains(new DateTimeImmutable('2015-06-02')));
        self::assertTrue($this->period->contains(new DateTimeImmutable('2015-06-10')));
        self::assertTrue($this->period->contains(new DateTimeImmutable('2015-06-18')));
        self::assertFalse($this->period->contains(new DateTimeImmutable('2016-06-19')));
        self::assertFalse($this->period->contains(new DateTimeImmutable('2016-07-15')));
    }

    public function testItHasDatePeriod(): void
    {
        $date1 = new DateTimeImmutable('2015-01-01');
        $date2 = new DateTimeImmutable('2015-01-11');
        $period = new RangePeriod($date1, $date2);

        self::assertInstanceOf(DatePeriod::class, $period->getDatePeriod(new DateInterval('P10D')));
        $i = 0;
        foreach ($period->getDatePeriod(new DateInterval('P1D')) as $x) {
            ++$i;
        }

        self::assertSame(11, $i);
    }

    public function testItHasDateInterval(): void
    {
        $date1 = new DateTimeImmutable('2015-01-01');
        $date2 = new DateTimeImmutable('2015-01-11');
        $period = new RangePeriod($date1, $date2);

        self::assertInstanceOf(DateInterval::class, $period->getDateInterval());
        self::assertEquals(
            (new DateInterval('P11D'))->format('y%y_m%m_d%d_h%h_i%i_s%s'),
            $period->getDateInterval()->format('y%y_m%m_d%d_h%h_i%i_s%s'),
        );
    }
}
