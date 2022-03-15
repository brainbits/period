<?php

declare(strict_types=1);

namespace Brainbits\PeriodTest\Serializer\Normalizer;

use Brainbits\Period\DayPeriod;
use Brainbits\Period\MonthPeriod;
use Brainbits\Period\Period;
use Brainbits\Period\RangePeriod;
use Brainbits\Period\RunningMonthPeriod;
use Brainbits\Period\RunningWeekPeriod;
use Brainbits\Period\RunningYearPeriod;
use Brainbits\Period\Serializer\Normalizer\PeriodNormalizer;
use Brainbits\Period\WeekPeriod;
use Brainbits\Period\YearPeriod;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

/**
 * @covers \Brainbits\Period\Serializer\Normalizer\PeriodNormalizer
 */
final class PeriodNormalizerTest extends TestCase
{
    public function testNormalizeThrowsExceptionOnInvalidClass(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $normalizer = new PeriodNormalizer();

        $normalizer->normalize(new stdClass());
    }

    public function testNormalize(): void
    {
        $normalizer = new PeriodNormalizer();

        $this->assertSame(
            'period:day:2018-02-03',
            $normalizer->normalize(DayPeriod::createFromPeriodString('2018-02-03')),
        );
        $this->assertSame(
            'period:week:2018-04',
            $normalizer->normalize(WeekPeriod::createFromPeriodString('2018-04')),
        );
        $this->assertSame(
            'period:month:2018-02',
            $normalizer->normalize(MonthPeriod::createFromPeriodString('2018-02')),
        );
        $this->assertSame(
            'period:year:2018',
            $normalizer->normalize(YearPeriod::createFromPeriodString('2018')),
        );
        $this->assertSame(
            'period:runningweek:current',
            $normalizer->normalize(RunningWeekPeriod::createCurrent()),
        );
        $this->assertSame(
            'period:runningmonth:current',
            $normalizer->normalize(RunningMonthPeriod::createCurrent()),
        );
        $this->assertSame(
            'period:runningyear:current',
            $normalizer->normalize(RunningYearPeriod::createCurrent()),
        );
        $this->assertSame(
            'period:range:2018-02-03_2018-02-05',
            $normalizer->normalize(new RangePeriod(
                new DateTimeImmutable('2018-02-03'),
                new DateTimeImmutable('2018-02-05'),
            )),
        );
    }

    public function testSupportsNormalization(): void
    {
        $normalizer = new PeriodNormalizer();

        $this->assertFalse($normalizer->supportsNormalization(new stdClass()));
        $this->assertTrue($normalizer->supportsNormalization(DayPeriod::createCurrent()));
        $this->assertTrue($normalizer->supportsNormalization(WeekPeriod::createCurrent()));
        $this->assertTrue($normalizer->supportsNormalization(MonthPeriod::createCurrent()));
        $this->assertTrue($normalizer->supportsNormalization(YearPeriod::createCurrent()));
        $this->assertTrue($normalizer->supportsNormalization(RunningWeekPeriod::createCurrent()));
        $this->assertTrue($normalizer->supportsNormalization(RunningMonthPeriod::createCurrent()));
        $this->assertTrue($normalizer->supportsNormalization(RunningYearPeriod::createCurrent()));
        $this->assertTrue($normalizer->supportsNormalization(new RangePeriod(
            new DateTimeImmutable(),
            new DateTimeImmutable('+1 week'),
        )));
    }

    public function testDenormalizeThrowsExceptionOnInvalidData(): void
    {
        $this->expectException(NotNormalizableValueException::class);
        $this->expectExceptionMessage('Data has to be a string, received object');

        $normalizer = new PeriodNormalizer();

        $normalizer->denormalize(new stdClass(), Period::class);
    }

    public function testDenormalizeThrowsExceptionOnInvalidRangePeriod(): void
    {
        $this->expectException(NotNormalizableValueException::class);
        $this->expectExceptionMessage('Not a valid range pattern: xx');

        $normalizer = new PeriodNormalizer();

        $normalizer->denormalize('period:range:xx', Period::class);
    }

    public function testDenormalizeThrowsExceptionOnInvalidPeriod(): void
    {
        $this->expectException(NotNormalizableValueException::class);

        $normalizer = new PeriodNormalizer();

        $normalizer->denormalize('period:month:2018', Period::class);
    }

    public function testDenormalizeThrowsExceptionOnInvalidType(): void
    {
        $this->expectException(NotNormalizableValueException::class);
        $this->expectExceptionMessage('Unknown type foo');

        $normalizer = new PeriodNormalizer();

        $normalizer->denormalize('period:foo:2018', Period::class);
    }

    public function testDenormalize(): void
    {
        $normalizer = new PeriodNormalizer();

        $this->assertEquals(
            DayPeriod::createFromPeriodString('2018-02-03'),
            $normalizer->denormalize('period:day:2018-02-03', Period::class),
        );
        $this->assertEquals(
            WeekPeriod::createFromPeriodString('2018-04'),
            $normalizer->denormalize('period:week:2018-04', Period::class),
        );
        $this->assertEquals(
            MonthPeriod::createFromPeriodString('2018-03'),
            $normalizer->denormalize('period:month:2018-03', Period::class),
        );
        $this->assertEquals(
            YearPeriod::createFromPeriodString('2018'),
            $normalizer->denormalize('period:year:2018', Period::class),
        );
        $this->assertEquals(
            RunningWeekPeriod::createCurrent(),
            $normalizer->denormalize('period:runningweek:current', Period::class),
        );
        $this->assertEquals(
            RunningMonthPeriod::createCurrent(),
            $normalizer->denormalize('period:runningmonth:current', Period::class),
        );
        $this->assertEquals(
            RunningYearPeriod::createCurrent(),
            $normalizer->denormalize('period:runningyear:current', Period::class),
        );
        $this->assertEquals(
            new RangePeriod(
                new DateTimeImmutable('2018-02-03'),
                new DateTimeImmutable('2018-02-05'),
            ),
            $normalizer->denormalize('period:range:2018-02-03_2018-02-05', Period::class),
        );
    }

    public function testSupportsDenormalization(): void
    {
        $normalizer = new PeriodNormalizer();

        $this->assertFalse($normalizer->supportsDenormalization(123, Period::class));
        $this->assertFalse($normalizer->supportsDenormalization('period:month:2018-10', stdClass::class));
        $this->assertTrue($normalizer->supportsDenormalization('period:month:2018-10', Period::class));
    }
}
