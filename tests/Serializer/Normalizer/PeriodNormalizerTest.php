<?php

declare(strict_types=1);

namespace Brainbits\PeriodTest\Serializer\Normalizer;

use Brainbits\Period\DayPeriod;
use Brainbits\Period\MonthPeriod;
use Brainbits\Period\Period;
use Brainbits\Period\PeriodFactory;
use Brainbits\Period\RangePeriod;
use Brainbits\Period\RunningMonthPeriod;
use Brainbits\Period\RunningWeekPeriod;
use Brainbits\Period\RunningYearPeriod;
use Brainbits\Period\Serializer\Normalizer\PeriodNormalizer;
use Brainbits\Period\WeekPeriod;
use Brainbits\Period\YearPeriod;
use DateTimeImmutable;
use Lcobucci\Clock\FrozenClock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

#[CoversClass(PeriodNormalizer::class)]
final class PeriodNormalizerTest extends TestCase
{
    private FrozenClock $clock;
    private PeriodFactory $periodFactory;
    private PeriodNormalizer $normalizer;

    public function setUp(): void
    {
        $this->clock = new FrozenClock(new DateTimeImmutable('2018-06-15 15:00:00'));
        $this->periodFactory = new PeriodFactory($this->clock);
        $this->normalizer = new PeriodNormalizer($this->periodFactory);
    }

    public function testNormalizeThrowsExceptionOnInvalidClass(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->normalizer->normalize(new stdClass());
    }

    public function testNormalize(): void
    {
        $now = $this->clock->now();

        $this->assertSame(
            'day#2018-02-03',
            $this->normalizer->normalize(DayPeriod::createFromPeriodString('2018-02-03')),
        );
        $this->assertSame(
            'week#2018-04',
            $this->normalizer->normalize(WeekPeriod::createFromPeriodString('2018-04')),
        );
        $this->assertSame(
            'month#2018-02',
            $this->normalizer->normalize(MonthPeriod::createFromPeriodString('2018-02')),
        );
        $this->assertSame(
            'year#2018',
            $this->normalizer->normalize(YearPeriod::createFromPeriodString('2018')),
        );
        $this->assertSame(
            'running-week#2018-04',
            $this->normalizer->normalize(RunningWeekPeriod::createFromPeriodString('2018-04', $now)),
        );
        $this->assertSame(
            'running-month#2018-06',
            $this->normalizer->normalize(RunningMonthPeriod::createFromPeriodString('2018-06', $now)),
        );
        $this->assertSame(
            'running-year#2018',
            $this->normalizer->normalize(RunningYearPeriod::createFromPeriodString('2018', $now)),
        );
        $this->assertSame(
            'range#2018-02-03#2018-02-05',
            $this->normalizer->normalize(new RangePeriod(
                new DateTimeImmutable('2018-02-03'),
                new DateTimeImmutable('2018-02-05'),
            )),
        );
    }

    public function testSupportsNormalization(): void
    {
        $this->assertFalse($this->normalizer->supportsNormalization(new stdClass()));
        $this->assertTrue($this->normalizer->supportsNormalization($this->periodFactory->currentDay()));
        $this->assertTrue($this->normalizer->supportsNormalization($this->periodFactory->currentWeek()));
        $this->assertTrue($this->normalizer->supportsNormalization($this->periodFactory->currentMonth()));
        $this->assertTrue($this->normalizer->supportsNormalization($this->periodFactory->currentYear()));
        $this->assertTrue($this->normalizer->supportsNormalization($this->periodFactory->currentRunningWeek()));
        $this->assertTrue($this->normalizer->supportsNormalization($this->periodFactory->currentRunningMonth()));
        $this->assertTrue($this->normalizer->supportsNormalization($this->periodFactory->currentYear()));
        $this->assertTrue($this->normalizer->supportsNormalization(new RangePeriod(
            new DateTimeImmutable(),
            new DateTimeImmutable('+1 week'),
        )));
    }

    public function testDenormalizeThrowsExceptionOnInvalidData(): void
    {
        $this->expectException(NotNormalizableValueException::class);
        $this->expectExceptionMessage('Data has to be a string, received object');

        $this->normalizer->denormalize(new stdClass(), Period::class);
    }

    public function testDenormalizeThrowsExceptionOnInvalidRangePeriod(): void
    {
        $this->expectException(NotNormalizableValueException::class);
        $this->expectExceptionMessage('period:range:xx is not a valid period identifier.');

        $this->normalizer->denormalize('period:range:xx', Period::class);
    }

    public function testDenormalizeThrowsExceptionOnInvalidPeriod(): void
    {
        $this->expectException(NotNormalizableValueException::class);

        $this->normalizer->denormalize('period:month:2018', Period::class);
    }

    public function testDenormalizeThrowsExceptionOnInvalidType(): void
    {
        $this->expectException(NotNormalizableValueException::class);
        $this->expectExceptionMessage('foo:2018 is not a valid period identifier.');

        $this->normalizer->denormalize('foo:2018', Period::class);
    }

    public function testDenormalize(): void
    {
        $now = $this->clock->now();

        $this->assertEquals(
            DayPeriod::createFromPeriodString('2018-02-03'),
            $this->normalizer->denormalize('day#2018-02-03', Period::class),
        );
        $this->assertEquals(
            WeekPeriod::createFromPeriodString('2018-04'),
            $this->normalizer->denormalize('week#2018-04', Period::class),
        );
        $this->assertEquals(
            MonthPeriod::createFromPeriodString('2018-03'),
            $this->normalizer->denormalize('month#2018-03', Period::class),
        );
        $this->assertEquals(
            YearPeriod::createFromPeriodString('2018'),
            $this->normalizer->denormalize('year#2018', Period::class),
        );
        $this->assertEquals(
            RunningWeekPeriod::createFromPeriodString('2018-04', $now),
            $this->normalizer->denormalize('running-week#2018-04', Period::class),
        );
        $this->assertEquals(
            RunningMonthPeriod::createFromPeriodString('2018-03', $now),
            $this->normalizer->denormalize('running-month#2018-03', Period::class),
        );
        $this->assertEquals(
            RunningYearPeriod::createFromPeriodString('2018', $now),
            $this->normalizer->denormalize('running-year#2018', Period::class),
        );
        $this->assertEquals(
            new RangePeriod(
                new DateTimeImmutable('2018-02-03'),
                new DateTimeImmutable('2018-02-05'),
            ),
            $this->normalizer->denormalize('range#2018-02-03#2018-02-05', Period::class),
        );
    }

    public function testSupportsDenormalization(): void
    {
        $this->assertFalse($this->normalizer->supportsDenormalization(123, Period::class));
        $this->assertFalse($this->normalizer->supportsDenormalization('month#2018-10', stdClass::class));
        $this->assertTrue($this->normalizer->supportsDenormalization('month#2018-10', Period::class));
    }
}
