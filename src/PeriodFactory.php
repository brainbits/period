<?php

declare(strict_types=1);

namespace Brainbits\Period;

use Brainbits\Period\Exception\InvalidPeriodIdentifier;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;

use function sprintf;
use function str_starts_with;

final class PeriodFactory
{
    public function __construct(private readonly ClockInterface $clock)
    {
    }

    public function currentDay(): DayPeriod
    {
        return new DayPeriod($this->clock->now());
    }

    public function currentWeek(): WeekPeriod
    {
        return new WeekPeriod($this->clock->now());
    }

    public function currentMonth(): MonthPeriod
    {
        return new MonthPeriod($this->clock->now());
    }

    public function currentYear(): YearPeriod
    {
        return new YearPeriod($this->clock->now());
    }

    public function currentRunningWeek(): RunningWeekPeriod
    {
        $now = $this->clock->now();

        $day = (int) $now->format('N') - 1;

        $date = $now->modify(sprintf('-%s day midnight', $day));

        return new RunningWeekPeriod($date, $now);
    }

    public function currentRunningMonth(): RunningMonthPeriod
    {
        $now = $this->clock->now();
        $date = $now->modify('first day of this month midnight');

        return new RunningMonthPeriod($date, $now);
    }

    public function currentRunningYear(): RunningYearPeriod
    {
        $now = $this->clock->now();
        $date = $now->modify('first day of january midnight');

        return new RunningYearPeriod($date, $now);
    }

    public function isCurrent(Period $period): bool
    {
        $now = $this->clock->now();

        return $period->contains($now);
    }

    /**
     * @param T $period
     *
     * @return T
     *
     * @template T of Period
     */
    public function next(Period $period): Period
    {
        $now = $this->clock->now();

        return match ($period::class) { // @phpstan-ignore-line
            YearPeriod::class => new YearPeriod($period->getStartDate()->modify('+1 year')),
            MonthPeriod::class => new MonthPeriod($period->getStartDate()->modify('+1 month')),
            WeekPeriod::class => new WeekPeriod($period->getStartDate()->modify('+1 week')),
            DayPeriod::class => new DayPeriod($period->getStartDate()->modify('+1 day')),
            RangePeriod::class => new RangePeriod(
                $period->getEndDate()->modify('+1 second'),
                $period->getEndDate()->add($period->getDateInterval()),
            ),
            RunningYearPeriod::class => new RunningYearPeriod($period->getStartDate()->modify('+1 year'), $now),
            RunningMonthPeriod::class => new RunningMonthPeriod($period->getStartDate()->modify('+1 month'), $now),
            RunningWeekPeriod::class => new RunningWeekPeriod($period->getStartDate()->modify('+1 week'), $now),
            default => throw InvalidPeriodIdentifier::unknownPeriod($period),
        };
    }

    /**
     * @param T $period
     *
     * @return T
     *
     * @template T of Period
     */
    public function previous(Period $period): Period
    {
        $now = $this->clock->now();

        return match ($period::class) { // @phpstan-ignore-line
            YearPeriod::class => new YearPeriod($period->getStartDate()->modify('-1 year')),
            MonthPeriod::class => new MonthPeriod($period->getStartDate()->modify('-1 month')),
            WeekPeriod::class => new WeekPeriod($period->getStartDate()->modify('-1 week')),
            DayPeriod::class => new DayPeriod($period->getStartDate()->modify('-1 day')),
            RangePeriod::class => new RangePeriod(
                $period->getStartDate()->sub($period->getDateInterval()),
                $period->getStartDate()->modify('-1 second'),
            ),
            RunningYearPeriod::class => new RunningYearPeriod($period->getStartDate()->modify('-1 year'), $now),
            RunningMonthPeriod::class => new RunningMonthPeriod($period->getStartDate()->modify('-1 month'), $now),
            RunningWeekPeriod::class => new RunningWeekPeriod($period->getStartDate()->modify('-1 week'), $now),
            default => throw InvalidPeriodIdentifier::unknownPeriod($period),
        };
    }

    /**
     * @param T $period
     *
     * @return T
     *
     * @template T of Period
     */
    public function current(Period $period): Period
    {
        $now = $this->clock->now();

        return match ($period::class) { // @phpstan-ignore-line
            YearPeriod::class => new YearPeriod($now),
            MonthPeriod::class => new MonthPeriod($now),
            WeekPeriod::class => new WeekPeriod($now),
            DayPeriod::class => new DayPeriod($now),
            RangePeriod::class => $this->determineCurrentRange($period, $now),
            RunningYearPeriod::class => new RunningYearPeriod($now, $now),
            RunningMonthPeriod::class => new RunningMonthPeriod($now, $now),
            RunningWeekPeriod::class => new RunningWeekPeriod($now, $now),
            default => throw InvalidPeriodIdentifier::unknownPeriod($period),
        };
    }

    private function determineCurrentRange(RangePeriod $period, DateTimeImmutable $now): RangePeriod
    {
        if ($period->contains($now)) {
            return new RangePeriod($period->getStartDate(), $period->getEndDate());
        }

        $newPeriod = $period;

        if ($period->getStartDate() < $now) {
            do {
                $newPeriod = $this->next($newPeriod);
            } while (!$newPeriod->contains($now));
        } else {
            do {
                $newPeriod = $this->previous($newPeriod);
            } while (!$newPeriod->contains($now));
        }

        return $newPeriod;
    }

    public function getTranslationKey(Period $period): string
    {
        $current = $this->current($period);
        $name = $period->getPeriodPrefix();

        $template = match (true) {
            $current->contains($period->getStartDate()) => 'period.%s.this',
            $this->next($current)->contains($period->getStartDate()) => 'period.%s.next',
            $this->previous($current)->contains($period->getStartDate()) => 'period.%s.prev',
            default => 'period.%s.period',
        };

        return sprintf($template, $name);
    }

    public function fromIdentifier(string $periodIdentifier): Period
    {
        $now = $this->clock->now();

        return match (true) {
            str_starts_with($periodIdentifier, 'day#') => DayPeriod::createFromPeriodIdentifier($periodIdentifier),
            str_starts_with($periodIdentifier, 'week#') => WeekPeriod::createFromPeriodIdentifier($periodIdentifier),
            str_starts_with($periodIdentifier, 'month#') => MonthPeriod::createFromPeriodIdentifier($periodIdentifier),
            str_starts_with($periodIdentifier, 'year#') => YearPeriod::createFromPeriodIdentifier($periodIdentifier),
            str_starts_with($periodIdentifier, 'range#') => RangePeriod::createFromPeriodIdentifier($periodIdentifier),
            str_starts_with($periodIdentifier, 'running-year#') => RunningYearPeriod::createFromPeriodIdentifier(
                $periodIdentifier,
                $now,
            ),
            str_starts_with($periodIdentifier, 'running-month#') => RunningMonthPeriod::createFromPeriodIdentifier(
                $periodIdentifier,
                $now,
            ),
            str_starts_with($periodIdentifier, 'running-week#') => RunningWeekPeriod::createFromPeriodIdentifier(
                $periodIdentifier,
                $now,
            ),
            default => throw InvalidPeriodIdentifier::unknownPeriodIdentifier($periodIdentifier),
        };
    }
}
