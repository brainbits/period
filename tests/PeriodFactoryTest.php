<?php

declare(strict_types=1);

namespace Brainbits\PeriodTest;

use Brainbits\Period\DayPeriod;
use Brainbits\Period\MonthPeriod;
use Brainbits\Period\Period;
use Brainbits\Period\PeriodFactory;
use Brainbits\Period\RangePeriod;
use Brainbits\Period\RunningMonthPeriod;
use Brainbits\Period\RunningWeekPeriod;
use Brainbits\Period\RunningYearPeriod;
use Brainbits\Period\WeekPeriod;
use Brainbits\Period\YearPeriod;
use DateTimeImmutable;
use Lcobucci\Clock\FrozenClock;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Brainbits\Period\PeriodFactory
 */
final class PeriodFactoryTest extends TestCase
{
    private PeriodFactory $factory;

    public function setUp(): void
    {
        $this->factory = new PeriodFactory(new FrozenClock(new DateTimeImmutable('2018-06-15 15:00:00')));
    }

    public function testCreateDay(): void
    {
        $day = $this->factory->currentDay();

        self::assertInstanceOf(DayPeriod::class, $day);
        self::assertEquals('2018-06-15T00:00:00+02:00', $day->getStartDate()->format('c'));
        self::assertEquals('2018-06-15T23:59:59+02:00', $day->getEndDate()->format('c'));
    }

    public function testCreateWek(): void
    {
        $week = $this->factory->currentWeek();

        self::assertInstanceOf(WeekPeriod::class, $week);
        self::assertEquals(new DateTimeImmutable('2018-06-11 00:00:00'), $week->getStartDate());
        self::assertEquals(new DateTimeImmutable('2018-06-17 23:59:59'), $week->getEndDate());
    }

    public function testCurrentMonth(): void
    {
        $month = $this->factory->currentMonth();

        self::assertInstanceOf(MonthPeriod::class, $month);
        self::assertEquals(new DateTimeImmutable('2018-06-01 00:00:00'), $month->getStartDate());
        self::assertEquals(new DateTimeImmutable('2018-06-30 23:59:59'), $month->getEndDate());
    }

    public function testCurrentYear(): void
    {
        $year = $this->factory->currentYear();

        self::assertInstanceOf(YearPeriod::class, $year);
        self::assertEquals(new DateTimeImmutable('2018-01-01 00:00:00'), $year->getStartDate());
        self::assertEquals(new DateTimeImmutable('2018-12-31 23:59:59'), $year->getEndDate());
    }

    public function testCurrentRunningWeek(): void
    {
        $runningWeek = $this->factory->currentRunningWeek();

        self::assertInstanceOf(RunningWeekPeriod::class, $runningWeek);
        self::assertEquals(new DateTimeImmutable('2018-06-11 00:00:00'), $runningWeek->getStartDate());
        self::assertEquals(new DateTimeImmutable('2018-06-15 23:59:59'), $runningWeek->getEndDate());
    }

    public function testCurrentRunningMonth(): void
    {
        $runningMonth = $this->factory->currentRunningMonth();

        self::assertInstanceOf(RunningMonthPeriod::class, $runningMonth);
        self::assertEquals(new DateTimeImmutable('2018-06-01 00:00:00'), $runningMonth->getStartDate());
        self::assertEquals(new DateTimeImmutable('2018-06-15 23:59:59'), $runningMonth->getEndDate());
    }

    public function testCurrentRunningYear(): void
    {
        $runningYear = $this->factory->currentRunningYear();

        self::assertInstanceOf(RunningYearPeriod::class, $runningYear);
        self::assertEquals(new DateTimeImmutable('2018-01-01 00:00:00'), $runningYear->getStartDate());
        self::assertEquals(new DateTimeImmutable('2018-06-15 23:59:59'), $runningYear->getEndDate());
    }

    /**
     * @return array<string, array{Period, bool}>
     */
    public function isCurrentPeriods(): array
    {
        return [
            'dayBefore' => [DayPeriod::createFromPeriodString('2018-06-10'), false],
            'daySame' => [DayPeriod::createFromPeriodString('2018-06-15'), true],
            'dayAfter' => [DayPeriod::createFromPeriodString('2018-06-20'), false],
            'weekBefore' => [WeekPeriod::createFromPeriodString('2018-23'), false],
            'weekSame' => [WeekPeriod::createFromPeriodString('2018-24'), true],
            'weekAfter' => [WeekPeriod::createFromPeriodString('2018-25'), false],
            'monthBefore' => [MonthPeriod::createFromPeriodString('2018-05'), false],
            'monthSame' => [MonthPeriod::createFromPeriodString('2018-06'), true],
            'monthAfter' => [MonthPeriod::createFromPeriodString('2018-07'), false],
            'yearBefore' => [YearPeriod::createFromPeriodString('2017'), false],
            'yearSame' => [YearPeriod::createFromPeriodString('2018'), true],
            'yearAfter' => [YearPeriod::createFromPeriodString('2019'), false],
            'rangeBefore' => [RangePeriod::createFromPeriodIdentifier('range#2018-05-10#2018-05-20'), false],
            'rangeSame' => [RangePeriod::createFromPeriodIdentifier('range#2018-06-10#2018-06-20'), true],
            'rangeAfter' => [RangePeriod::createFromPeriodIdentifier('range#2018-07-10#2018-07-20'), false],
            'runningWeekBefore' => [
                RunningWeekPeriod::createFromPeriodString(
                    '2018-23',
                    new DateTimeImmutable('2018-06-15 15:00:00'),
                ),
                false,
            ],
            'runningWeekSame' => [
                RunningWeekPeriod::createFromPeriodString(
                    '2018-24',
                    new DateTimeImmutable('2018-06-15 15:00:00'),
                ),
                true,
            ],
            'runningWeekAfter' => [
                RunningWeekPeriod::createFromPeriodString(
                    '2018-25',
                    new DateTimeImmutable('2018-06-15 15:00:00'),
                ),
                false,
            ],
            'runningWeekExBefore' => [
                RunningWeekPeriod::createFromPeriodString(
                    '2018-23',
                    new DateTimeImmutable('2018-06-14 15:00:00'),
                ),
                false,
            ],
            'runningWeekExSame' => [
                RunningWeekPeriod::createFromPeriodString(
                    '2018-24',
                    new DateTimeImmutable('2018-06-14 15:00:00'),
                ),
                false,
            ],
            'runningWeekExAfter' => [
                RunningWeekPeriod::createFromPeriodString(
                    '2018-25',
                    new DateTimeImmutable('2018-06-14 15:00:00'),
                ),
                false,
            ],
            'runningMonthBefore' => [
                RunningMonthPeriod::createFromPeriodString(
                    '2018-05',
                    new DateTimeImmutable('2018-07-15 15:00:00'),
                ),
                false,
            ],
            'runningMonthSame' => [
                RunningMonthPeriod::createFromPeriodString(
                    '2018-06',
                    new DateTimeImmutable('2018-07-15 15:00:00'),
                ),
                true,
            ],
            'runningMonthAfter' => [
                RunningMonthPeriod::createFromPeriodString(
                    '2018-07',
                    new DateTimeImmutable('2018-07-15 15:00:00'),
                ),
                false,
            ],
            'runningMonthExBefore' => [
                RunningMonthPeriod::createFromPeriodString(
                    '2018-05',
                    new DateTimeImmutable('2018-06-14 15:00:00'),
                ),
                false,
            ],
            'runningMonthExSame' => [
                RunningMonthPeriod::createFromPeriodString(
                    '2018-06',
                    new DateTimeImmutable('2018-06-14 15:00:00'),
                ),
                false,
            ],
            'runningMonthExAfter' => [
                RunningMonthPeriod::createFromPeriodString(
                    '2018-07',
                    new DateTimeImmutable('2018-06-14 15:00:00'),
                ),
                false,
            ],
            'runningYearBefore' => [
                RunningYearPeriod::createFromPeriodString(
                    '2017',
                    new DateTimeImmutable('2019-07-15 15:00:00'),
                ),
                false,
            ],
            'runningYearSame' => [
                RunningYearPeriod::createFromPeriodString(
                    '2018',
                    new DateTimeImmutable('2019-07-15 15:00:00'),
                ),
                true,
            ],
            'runningYearAfter' => [
                RunningYearPeriod::createFromPeriodString(
                    '2019',
                    new DateTimeImmutable('2019-07-15 15:00:00'),
                ),
                false,
            ],
            'runningYearExBefore' => [
                RunningYearPeriod::createFromPeriodString(
                    '2017',
                    new DateTimeImmutable('2018-06-14 15:00:00'),
                ),
                false,
            ],
            'runningYearExSame' => [
                RunningYearPeriod::createFromPeriodString(
                    '2018',
                    new DateTimeImmutable('2018-06-14 15:00:00'),
                ),
                false,
            ],
            'runningYearExAfter' => [
                RunningYearPeriod::createFromPeriodString(
                    '2019',
                    new DateTimeImmutable('2018-06-14 15:00:00'),
                ),
                false,
            ],
        ];
    }

    /**
     * @dataProvider isCurrentPeriods
     */
    public function testIsCurrent(Period $period, bool $expected): void
    {
        self::assertSame($expected, $this->factory->isCurrent($period));
    }

    /**
     * @return array<string, array{Period, string, string}>
     */
    public function nextPeriods(): array
    {
        $now1 = new DateTimeImmutable('2018-06-16 15:00:00'); // saturday
        $now2 = new DateTimeImmutable('2019-07-16 15:00:00');

        return [
            'day' => [
                DayPeriod::createFromPeriodString('2018-06-15'),
                '2018-06-16T00:00:00+02:00',
                '2018-06-16T23:59:59+02:00',
            ],
            'week' => [
                WeekPeriod::createFromPeriodString('2018-24'),
                '2018-06-18T00:00:00+02:00',
                '2018-06-24T23:59:59+02:00',
            ],
            'month' => [
                MonthPeriod::createFromPeriodString('2018-06'),
                '2018-07-01T00:00:00+02:00',
                '2018-07-31T23:59:59+02:00',
            ],
            'year' => [
                YearPeriod::createFromPeriodString('2018'),
                '2019-01-01T00:00:00+01:00',
                '2019-12-31T23:59:59+01:00',
            ],
            'range' => [
                RangePeriod::createFromPeriodIdentifier('range#2018-06-10#2018-06-19'),
                '2018-06-20T00:00:00+02:00',
                '2018-06-29T23:59:59+02:00',
            ],
            'runningWeek' => [
                RunningWeekPeriod::createFromPeriodString('2018-24', $now1),
                '2018-06-18T00:00:00+02:00',
                '2018-06-24T23:59:59+02:00',
            ],
            'runningMonth' => [
                RunningMonthPeriod::createFromPeriodString('2018-06', $now1),
                '2018-07-01T00:00:00+02:00',
                '2018-07-31T23:59:59+02:00',
            ],
            'runningYear' => [
                RunningYearPeriod::createFromPeriodString('2018', $now1),
                '2019-01-01T00:00:00+01:00',
                '2019-12-31T23:59:59+01:00',
            ],
            'runningWeekFull' => [
                RunningWeekPeriod::createFromPeriodString('2018-24', $now2),
                '2018-06-18T00:00:00+02:00',
                '2018-06-24T23:59:59+02:00',
            ],
            'runningMonthFull' => [
                RunningMonthPeriod::createFromPeriodString('2018-06', $now2),
                '2018-07-01T00:00:00+02:00',
                '2018-07-31T23:59:59+02:00',
            ],
            'runningYearFull' => [
                RunningYearPeriod::createFromPeriodString('2018', $now2),
                '2019-01-01T00:00:00+01:00',
                '2019-12-31T23:59:59+01:00',
            ],
        ];
    }

    /**
     * @dataProvider nextPeriods
     */
    public function testNext(Period $period, string $expectedStart, string $expectedEnd): void
    {
        self::assertSame($expectedStart, $this->factory->next($period)->getStartDate()->format('c'));
        self::assertSame($expectedEnd, $this->factory->next($period)->getEndDate()->format('c'));
    }

    /**
     * @return array<string, array{Period, string, string}>
     */
    public function previousPeriods(): array
    {
        $now1 = new DateTimeImmutable('2018-06-16 15:00:00'); // saturday
        $now2 = new DateTimeImmutable('2019-07-16 15:00:00');

        return [
            'day' => [
                DayPeriod::createFromPeriodString('2018-06-15'),
                '2018-06-14T00:00:00+02:00',
                '2018-06-14T23:59:59+02:00',
            ],
            'week' => [
                WeekPeriod::createFromPeriodString('2018-24'),
                '2018-06-04T00:00:00+02:00',
                '2018-06-10T23:59:59+02:00',
            ],
            'month' => [
                MonthPeriod::createFromPeriodString('2018-06'),
                '2018-05-01T00:00:00+02:00',
                '2018-05-31T23:59:59+02:00',
            ],
            'year' => [
                YearPeriod::createFromPeriodString('2018'),
                '2017-01-01T00:00:00+01:00',
                '2017-12-31T23:59:59+01:00',
            ],
            'range' => [
                RangePeriod::createFromPeriodIdentifier('range#2018-06-10#2018-06-19'),
                '2018-05-31T00:00:00+02:00',
                '2018-06-09T23:59:59+02:00',
            ],
            'runningWeek' => [
                RunningWeekPeriod::createFromPeriodString('2018-24', $now1),
                '2018-06-04T00:00:00+02:00',
                '2018-06-10T23:59:59+02:00',
            ],
            'runningMonth' => [
                RunningMonthPeriod::createFromPeriodString('2018-06', $now1),
                '2018-05-01T00:00:00+02:00',
                '2018-05-31T23:59:59+02:00',
            ],
            'runningYear' => [
                RunningYearPeriod::createFromPeriodString('2018', $now1),
                '2017-01-01T00:00:00+01:00',
                '2017-12-31T23:59:59+01:00',
            ],
            'runningWeekFull' => [
                RunningWeekPeriod::createFromPeriodString('2018-24', $now2),
                '2018-06-04T00:00:00+02:00',
                '2018-06-10T23:59:59+02:00',
            ],
            'runningMonthFull' => [
                RunningMonthPeriod::createFromPeriodString('2018-06', $now2),
                '2018-05-01T00:00:00+02:00',
                '2018-05-31T23:59:59+02:00',
            ],
            'runningYearFull' => [
                RunningYearPeriod::createFromPeriodString('2018', $now2),
                '2017-01-01T00:00:00+01:00',
                '2017-12-31T23:59:59+01:00',
            ],
        ];
    }

    /**
     * @dataProvider previousPeriods
     */
    public function testPrevious(Period $period, string $expectedStart, string $expectedEnd): void
    {
        self::assertSame($expectedStart, $this->factory->previous($period)->getStartDate()->format('c'));
        self::assertSame($expectedEnd, $this->factory->previous($period)->getEndDate()->format('c'));
    }

    /**
     * @return array<string, array{Period, string, string}>
     */
    public function currentPeriods(): array
    {
        $now1 = new DateTimeImmutable('2017-06-16 15:00:00'); // saturday
        $now2 = new DateTimeImmutable('2018-07-16 15:00:00');

        return [
            'day' => [
                DayPeriod::createFromPeriodString('2017-06-15'),
                '2018-06-15T00:00:00+02:00',
                '2018-06-15T23:59:59+02:00',
            ],
            'week' => [
                WeekPeriod::createFromPeriodString('2017-24'),
                '2018-06-11T00:00:00+02:00',
                '2018-06-17T23:59:59+02:00',
            ],
            'month' => [
                MonthPeriod::createFromPeriodString('2017-06'),
                '2018-06-01T00:00:00+02:00',
                '2018-06-30T23:59:59+02:00',
            ],
            'year' => [
                YearPeriod::createFromPeriodString('2017'),
                '2018-01-01T00:00:00+01:00',
                '2018-12-31T23:59:59+01:00',
            ],
            'range' => [
                RangePeriod::createFromPeriodIdentifier('range#2017-06-10#2017-06-20'),
                '2018-06-08T00:00:00+02:00',
                '2018-06-18T23:59:59+02:00',
            ],
            'runningWeek' => [
                RunningWeekPeriod::createFromPeriodString('2017-24', $now1),
                '2018-06-11T00:00:00+02:00',
                '2018-06-15T23:59:59+02:00',
            ],
            'runningMonth' => [
                RunningMonthPeriod::createFromPeriodString('2017-06', $now1),
                '2018-06-01T00:00:00+02:00',
                '2018-06-15T23:59:59+02:00',
            ],
            'runningYear' => [
                RunningYearPeriod::createFromPeriodString('2017', $now1),
                '2018-01-01T00:00:00+01:00',
                '2018-06-15T23:59:59+02:00',
            ],
            'runningWeekFull' => [
                RunningWeekPeriod::createFromPeriodString('2017-24', $now2),
                '2018-06-11T00:00:00+02:00',
                '2018-06-15T23:59:59+02:00',
            ],
            'runningMonthFull' => [
                RunningMonthPeriod::createFromPeriodString('2017-06', $now2),
                '2018-06-01T00:00:00+02:00',
                '2018-06-15T23:59:59+02:00',
            ],
            'runningYearFull' => [
                RunningYearPeriod::createFromPeriodString('2017', $now2),
                '2018-01-01T00:00:00+01:00',
                '2018-06-15T23:59:59+02:00',
            ],
        ];
    }

    /**
     * @dataProvider currentPeriods
     */
    public function testCurrent(Period $period, string $expectedStart, string $expectedEnd): void
    {
        self::assertSame($expectedStart, $this->factory->current($period)->getStartDate()->format('c'));
        self::assertSame($expectedEnd, $this->factory->current($period)->getEndDate()->format('c'));
    }

    /**
     * @return array<string, array{Period, string}>
     */
    public function translationPeriods(): array
    {
        return [
            'dayBefore' => [DayPeriod::createFromPeriodString('2018-06-13'), 'period.day.period'],
            'dayPrev' => [DayPeriod::createFromPeriodString('2018-06-14'), 'period.day.prev'],
            'daySame' => [DayPeriod::createFromPeriodString('2018-06-15'), 'period.day.this'],
            'dayNext' => [DayPeriod::createFromPeriodString('2018-06-16'), 'period.day.next'],
            'dayAfter' => [DayPeriod::createFromPeriodString('2018-06-17'), 'period.day.period'],
            'weekBefore' => [WeekPeriod::createFromPeriodString('2018-22'), 'period.week.period'],
            'weekPrev' => [WeekPeriod::createFromPeriodString('2018-23'), 'period.week.prev'],
            'weekSame' => [WeekPeriod::createFromPeriodString('2018-24'), 'period.week.this'],
            'weekNext' => [WeekPeriod::createFromPeriodString('2018-25'), 'period.week.next'],
            'weekAfter' => [WeekPeriod::createFromPeriodString('2018-26'), 'period.week.period'],
            'monthBefore' => [MonthPeriod::createFromPeriodString('2018-04'), 'period.month.period'],
            'monthPrev' => [MonthPeriod::createFromPeriodString('2018-05'), 'period.month.prev'],
            'monthSame' => [MonthPeriod::createFromPeriodString('2018-06'), 'period.month.this'],
            'monthNext' => [MonthPeriod::createFromPeriodString('2018-07'), 'period.month.next'],
            'monthAfter' => [MonthPeriod::createFromPeriodString('2018-08'), 'period.month.period'],
            'yearBefore' => [YearPeriod::createFromPeriodString('2016'), 'period.year.period'],
            'yearPrev' => [YearPeriod::createFromPeriodString('2017'), 'period.year.prev'],
            'yearSame' => [YearPeriod::createFromPeriodString('2018'), 'period.year.this'],
            'yearNext' => [YearPeriod::createFromPeriodString('2019'), 'period.year.next'],
            'yearAfter' => [YearPeriod::createFromPeriodString('2020'), 'period.year.period'],
            'rangeBefore' => [
                RangePeriod::createFromPeriodIdentifier('range#2018-05-21#2018-05-31'),
                'period.range.period',
            ],
            'rangePrev' => [
                RangePeriod::createFromPeriodIdentifier('range#2018-05-31#2018-06-10'),
                'period.range.prev',
            ],
            'rangeSame' => [
                RangePeriod::createFromPeriodIdentifier('range#2018-06-10#2018-06-20'),
                'period.range.this',
            ],
            'rangeNext' => [
                RangePeriod::createFromPeriodIdentifier('range#2018-06-20#2018-06-30'),
                'period.range.next',
            ],
            'rangeAfter' => [
                RangePeriod::createFromPeriodIdentifier('range#2018-06-30#2018-07-09'),
                'period.range.period',
            ],
            'runningWeekBefore' => [
                RunningWeekPeriod::createFromPeriodString(
                    '2018-22',
                    new DateTimeImmutable('2018-06-02'),
                ),
                'period.running-week.period',
            ],
            'runningWeekPrev' => [
                RunningWeekPeriod::createFromPeriodString(
                    '2018-23',
                    new DateTimeImmutable('2018-06-02'),
                ),
                'period.running-week.prev',
            ],
            'runningWeekSame' => [
                RunningWeekPeriod::createFromPeriodString(
                    '2018-24',
                    new DateTimeImmutable('2018-06-02'),
                ),
                'period.running-week.this',
            ],
            'runningWeekNext' => [
                RunningWeekPeriod::createFromPeriodString(
                    '2018-25',
                    new DateTimeImmutable('2018-06-02'),
                ),
                'period.running-week.next',
            ],
            'runningWeekAfter' => [
                RunningWeekPeriod::createFromPeriodString(
                    '2018-26',
                    new DateTimeImmutable('2018-06-02'),
                ),
                'period.running-week.period',
            ],
            'runningMonthBefore' => [
                RunningMonthPeriod::createFromPeriodString(
                    '2018-04',
                    new DateTimeImmutable('2018-06-02'),
                ),
                'period.running-month.period',
            ],
            'runningMonthPrev' => [
                RunningMonthPeriod::createFromPeriodString(
                    '2018-05',
                    new DateTimeImmutable('2018-06-02'),
                ),
                'period.running-month.prev',
            ],
            'runningMonthSame' => [
                RunningMonthPeriod::createFromPeriodString(
                    '2018-06',
                    new DateTimeImmutable('2018-06-02'),
                ),
                'period.running-month.this',
            ],
            'runningMonthNext' => [
                RunningMonthPeriod::createFromPeriodString(
                    '2018-07',
                    new DateTimeImmutable('2018-06-02'),
                ),
                'period.running-month.next',
            ],
            'runningMonthAfter' => [
                RunningMonthPeriod::createFromPeriodString(
                    '2018-08',
                    new DateTimeImmutable('2018-06-02'),
                ),
                'period.running-month.period',
            ],
            'runningYearBefore' => [
                RunningYearPeriod::createFromPeriodString(
                    '2016',
                    new DateTimeImmutable('2018-06-02'),
                ),
                'period.running-year.period',
            ],
            'runningYearPrev' => [
                RunningYearPeriod::createFromPeriodString(
                    '2017',
                    new DateTimeImmutable('2018-06-02'),
                ),
                'period.running-year.prev',
            ],
            'runningYearSame' => [
                RunningYearPeriod::createFromPeriodString(
                    '2018',
                    new DateTimeImmutable('2018-06-02'),
                ),
                'period.running-year.this',
            ],
            'runningYearNext' => [
                RunningYearPeriod::createFromPeriodString(
                    '2019',
                    new DateTimeImmutable('2018-06-02'),
                ),
                'period.running-year.next',
            ],
            'runningYearAfter' => [
                RunningYearPeriod::createFromPeriodString(
                    '2020',
                    new DateTimeImmutable('2018-06-02'),
                ),
                'period.running-year.period',
            ],
        ];
    }

    public function testFromIdentifier(): void
    {
        $this->assertEquals(
            DayPeriod::createFromPeriodString('2022-02-02'),
            $this->factory->fromIdentifier('day#2022-02-02'),
        );
        $this->assertEquals(
            WeekPeriod::createFromPeriodString('2022-14'),
            $this->factory->fromIdentifier('week#2022-14'),
        );
        $this->assertEquals(
            MonthPeriod::createFromPeriodString('2022-05'),
            $this->factory->fromIdentifier('month#2022-05'),
        );
        $this->assertEquals(
            YearPeriod::createFromPeriodString('2021'),
            $this->factory->fromIdentifier('year#2021'),
        );
        $this->assertEquals(
            new RangePeriod(new DateTimeImmutable('2022-02-02'), new DateTimeImmutable('2023-03-03')),
            $this->factory->fromIdentifier('range#2022-02-02#2023-03-03'),
        );
        $this->assertEquals(
            RunningWeekPeriod::createFromPeriodString('2018-19', new DateTimeImmutable('2018-06-15')),
            $this->factory->fromIdentifier('running-week#2018-19'),
        );
        $this->assertEquals(
            RunningMonthPeriod::createFromPeriodString('2018-05', new DateTimeImmutable('2018-06-15')),
            $this->factory->fromIdentifier('running-month#2018-05'),
        );
        $this->assertEquals(
            RunningYearPeriod::createFromPeriodString('2018', new DateTimeImmutable('2018-06-15')),
            $this->factory->fromIdentifier('running-year#2018'),
        );
    }

    /**
     * @dataProvider translationPeriods
     */
    public function testTranslationPeriods(Period $period, string $expected): void
    {
        self::assertSame($expected, $this->factory->getTranslationKey($period));
    }
}
