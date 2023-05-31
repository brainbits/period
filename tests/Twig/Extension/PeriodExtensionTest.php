<?php

declare(strict_types=1);

namespace Brainbits\PeriodTest\Twig\Extension;

use Brainbits\Period\DayPeriod;
use Brainbits\Period\MonthPeriod;
use Brainbits\Period\PeriodFactory;
use Brainbits\Period\RangePeriod;
use Brainbits\Period\RunningMonthPeriod;
use Brainbits\Period\RunningWeekPeriod;
use Brainbits\Period\RunningYearPeriod;
use Brainbits\Period\Twig\Extension\PeriodExtension;
use Brainbits\Period\WeekPeriod;
use Brainbits\Period\YearPeriod;
use DateTimeImmutable;
use Lcobucci\Clock\FrozenClock;
use PHPUnit\Framework\TestCase;

/** @covers \Brainbits\Period\Twig\Extension\PeriodExtension */
final class PeriodExtensionTest extends TestCase
{
    private FrozenClock $clock;
    private PeriodFactory $periodFactory;
    private PeriodExtension $extension;

    public function setUp(): void
    {
        $this->clock = new FrozenClock(new DateTimeImmutable('2018-06-15 15:00:00'));
        $this->periodFactory = new PeriodFactory($this->clock);
        $this->extension = new PeriodExtension($this->periodFactory);
    }

    public function testGetFunctions(): void
    {
        $this->assertIsArray($this->extension->getFunctions());
    }

    public function testCurrent(): void
    {
        $this->assertEquals($this->periodFactory->currentDay(), $this->extension->currentDayPeriod());
        $this->assertEquals($this->periodFactory->currentWeek(), $this->extension->currentWeekPeriod());
        $this->assertEquals($this->periodFactory->currentMonth(), $this->extension->currentMonthPeriod());
        $this->assertEquals($this->periodFactory->currentYear(), $this->extension->currentYearPeriod());
        $this->assertEquals($this->periodFactory->currentRunningWeek(), $this->extension->currentRunningWeekPeriod());
        $this->assertEquals($this->periodFactory->currentRunningMonth(), $this->extension->currentRunningMonthPeriod());
        $this->assertEquals($this->periodFactory->currentRunningYear(), $this->extension->currentRunningYearPeriod());
    }

    public function testCurrentPeriod(): void
    {
        $this->assertEquals(
            $this->periodFactory->currentDay(),
            $this->extension->currentPeriod($this->periodFactory->currentDay()),
        );
        $this->assertEquals(
            $this->periodFactory->currentWeek(),
            $this->extension->currentPeriod($this->periodFactory->currentWeek()),
        );
        $this->assertEquals(
            $this->periodFactory->currentMonth(),
            $this->extension->currentPeriod($this->periodFactory->currentMonth()),
        );
        $this->assertEquals(
            $this->periodFactory->currentYear(),
            $this->extension->currentPeriod($this->periodFactory->currentYear()),
        );
        $this->assertEquals(
            $this->periodFactory->currentRunningWeek(),
            $this->extension->currentPeriod($this->periodFactory->currentRunningWeek()),
        );
        $this->assertEquals(
            $this->periodFactory->currentRunningMonth(),
            $this->extension->currentPeriod($this->periodFactory->currentRunningMonth()),
        );
        $this->assertEquals(
            $this->periodFactory->currentRunningYear(),
            $this->extension->currentPeriod($this->periodFactory->currentRunningYear()),
        );
    }

    public function testNextPeriod(): void
    {
        $this->assertEquals(
            $this->periodFactory->next($this->periodFactory->currentDay()),
            $this->extension->nextPeriod($this->periodFactory->currentDay()),
        );
        $this->assertEquals(
            $this->periodFactory->next($this->periodFactory->currentWeek()),
            $this->extension->nextPeriod($this->periodFactory->currentWeek()),
        );
        $this->assertEquals(
            $this->periodFactory->next($this->periodFactory->currentMonth()),
            $this->extension->nextPeriod($this->periodFactory->currentMonth()),
        );
        $this->assertEquals(
            $this->periodFactory->next($this->periodFactory->currentYear()),
            $this->extension->nextPeriod($this->periodFactory->currentYear()),
        );
        $this->assertEquals(
            $this->periodFactory->next($this->periodFactory->currentRunningWeek()),
            $this->extension->nextPeriod($this->periodFactory->currentRunningWeek()),
        );
        $this->assertEquals(
            $this->periodFactory->next($this->periodFactory->currentRunningMonth()),
            $this->extension->nextPeriod($this->periodFactory->currentRunningMonth()),
        );
        $this->assertEquals(
            $this->periodFactory->next($this->periodFactory->currentRunningYear()),
            $this->extension->nextPeriod($this->periodFactory->currentRunningYear()),
        );
    }

    public function testPreviousPeriod(): void
    {
        $this->assertEquals(
            $this->periodFactory->previous($this->periodFactory->currentDay()),
            $this->extension->previousPeriod($this->periodFactory->currentDay()),
        );
        $this->assertEquals(
            $this->periodFactory->previous($this->periodFactory->currentWeek()),
            $this->extension->previousPeriod($this->periodFactory->currentWeek()),
        );
        $this->assertEquals(
            $this->periodFactory->previous($this->periodFactory->currentMonth()),
            $this->extension->previousPeriod($this->periodFactory->currentMonth()),
        );
        $this->assertEquals(
            $this->periodFactory->previous($this->periodFactory->currentYear()),
            $this->extension->previousPeriod($this->periodFactory->currentYear()),
        );
        $this->assertEquals(
            $this->periodFactory->previous($this->periodFactory->currentRunningWeek()),
            $this->extension->previousPeriod($this->periodFactory->currentRunningWeek()),
        );
        $this->assertEquals(
            $this->periodFactory->previous($this->periodFactory->currentRunningMonth()),
            $this->extension->previousPeriod($this->periodFactory->currentRunningMonth()),
        );
        $this->assertEquals(
            $this->periodFactory->previous($this->periodFactory->currentRunningYear()),
            $this->extension->previousPeriod($this->periodFactory->currentRunningYear()),
        );
    }

    public function testIsCurrent(): void
    {
        $this->assertTrue($this->periodFactory->isCurrent($this->extension->currentDayPeriod()));
        $this->assertTrue($this->periodFactory->isCurrent($this->extension->currentWeekPeriod()));
        $this->assertTrue($this->periodFactory->isCurrent($this->extension->currentMonthPeriod()));
        $this->assertTrue($this->periodFactory->isCurrent($this->extension->currentYearPeriod()));
        $this->assertTrue($this->periodFactory->isCurrent($this->extension->currentRunningWeekPeriod()));
        $this->assertTrue($this->periodFactory->isCurrent($this->extension->currentRunningMonthPeriod()));
        $this->assertTrue($this->periodFactory->isCurrent($this->extension->currentRunningYearPeriod()));
    }

    public function testFromIdentifier(): void
    {
        $this->assertEquals(
            'period.day.period',
            $this->extension->periodTranslationKey(DayPeriod::createFromPeriodString('2022-02-02')),
        );
        $this->assertEquals(
            'period.week.period',
            $this->extension->periodTranslationKey(WeekPeriod::createFromPeriodString('2022-14')),
        );
        $this->assertEquals(
            'period.month.period',
            $this->extension->periodTranslationKey(MonthPeriod::createFromPeriodString('2022-05')),
        );
        $this->assertEquals(
            'period.year.period',
            $this->extension->periodTranslationKey(YearPeriod::createFromPeriodString('2021')),
        );
        $this->assertEquals(
            'period.range.period',
            // phpcs:ignore Generic.Files.LineLength.TooLong
            $this->extension->periodTranslationKey(new RangePeriod(new DateTimeImmutable('2022-02-02'), new DateTimeImmutable('2023-03-03'))),
        );
        $this->assertEquals(
            'period.running-week.period',
            // phpcs:ignore Generic.Files.LineLength.TooLong
            $this->extension->periodTranslationKey(RunningWeekPeriod::createFromPeriodString('2018-19', new DateTimeImmutable('2018-06-15'))),
        );
        $this->assertEquals(
            'period.running-month.prev',
            // phpcs:ignore Generic.Files.LineLength.TooLong
            $this->extension->periodTranslationKey(RunningMonthPeriod::createFromPeriodString('2018-05', new DateTimeImmutable('2018-06-15'))),
        );
        $this->assertEquals(
            'period.running-year.this',
            // phpcs:ignore Generic.Files.LineLength.TooLong
            $this->extension->periodTranslationKey(RunningYearPeriod::createFromPeriodString('2018', new DateTimeImmutable('2018-06-15'))),
        );
    }
}
