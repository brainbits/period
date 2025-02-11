<?php

declare(strict_types=1);

namespace Brainbits\PeriodTest;

use Brainbits\Period\Exception\InvalidDateString;
use Brainbits\Period\Exception\InvalidPeriodIdentifier;
use Brainbits\Period\Exception\InvalidPeriodString;
use Brainbits\Period\YearPeriod;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(YearPeriod::class)]
final class YearPeriodTest extends TestCase
{
    public function testItIsInitializable(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new YearPeriod($date);
        $this->assertEquals('2015-01-01T00:00:00+01:00', $period->getStartDate()->format('c'));
    }

    public function testItIsInitializableWithPeriodIdentifier(): void
    {
        $period = YearPeriod::createFromPeriodIdentifier('year#2015');

        self::assertEquals('2015-01-01T00:00:00+01:00', $period->getStartDate()->format('c'));
    }

    public function testItIsNotInitializableWithInvalidPeriodIdentifier(): void
    {
        $this->expectException(InvalidPeriodIdentifier::class);
        $this->expectExceptionMessage('foo#bar is not a valid year period identifier (e.g. year#2017).');

        YearPeriod::createFromPeriodIdentifier('foo#bar');
    }

    public function testItIsInitializableWithPeriod(): void
    {
        $period = YearPeriod::createFromPeriodString('2015');
        $this->assertEquals('2015-01-01T00:00:00+01:00', $period->getStartDate()->format('c'));
    }

    public function testItIsNotInitializableWithInvalidPeriod(): void
    {
        $this->expectException(InvalidPeriodString::class);

        YearPeriod::createFromPeriodString('15');
    }

    public function testItIsInitializableWithDate(): void
    {
        $period = YearPeriod::createFromDateString('2015-01-08');
        $this->assertEquals('2015-01-01T00:00:00+01:00', $period->getStartDate()->format('c'));
    }

    public function testItIsNotInitializableWithInvalidDate(): void
    {
        $this->expectException(InvalidDateString::class);

        YearPeriod::createFromDateString('2015.01.08');
    }

    public function testItIsInitializableWithYear(): void
    {
        $period = YearPeriod::createFromYear(2015);
        $this->assertEquals(new DateTimeImmutable('2015-01-01'), $period->getStartDate());
    }

    public function testItHasAStartDate(): void
    {
        $period = YearPeriod::createFromPeriodString('2015');
        $this->assertEquals('2015-01-01T00:00:00+01:00', $period->getStartDate()->format('c'));
    }

    public function testItHasAnEndDate(): void
    {
        $date = new DateTimeImmutable('2015-01-01');
        $period = new YearPeriod($date);
        $this->assertEquals('2015-12-31T23:59:59+01:00', $period->getEndDate()->format('c'));
    }

    public function testItHasAPeriodString(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new YearPeriod($date);
        $this->assertSame('2015', $period->getPeriodString());
    }

    public function testItHasAPeriodIdentifier(): void
    {
        $date = new DateTimeImmutable('2015-01-07');
        $period = new YearPeriod($date);
        $this->assertSame('year#2015', $period->getPeriodIdentifier());
    }

    public function testItContainsDate(): void
    {
        $period = YearPeriod::createFromPeriodString('2015');

        self::assertFalse($period->contains(new DateTimeImmutable('2014-07-07')));
        self::assertFalse($period->contains(new DateTimeImmutable('2014-12-31')));
        self::assertTrue($period->contains(new DateTimeImmutable('2015-01-01')));
        self::assertTrue($period->contains(new DateTimeImmutable('2015-06-06')));
        self::assertTrue($period->contains(new DateTimeImmutable('2015-12-31')));
        self::assertFalse($period->contains(new DateTimeImmutable('2016-01-01')));
        self::assertFalse($period->contains(new DateTimeImmutable('2016-07-01')));
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
        $this->assertEquals(
            (new DateInterval('P1Y'))->format('y%y_m%m_d%d_h%h_i%i_s%s'),
            $period->getDateInterval()->format('y%y_m%m_d%d_h%h_i%i_s%s'),
        );
    }
}
