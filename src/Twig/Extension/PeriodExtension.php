<?php

declare(strict_types=1);

namespace Brainbits\Period\Twig\Extension;

use Brainbits\Period\DayPeriod;
use Brainbits\Period\MonthPeriod;
use Brainbits\Period\Period;
use Brainbits\Period\PeriodFactory;
use Brainbits\Period\RunningMonthPeriod;
use Brainbits\Period\RunningWeekPeriod;
use Brainbits\Period\RunningYearPeriod;
use Brainbits\Period\WeekPeriod;
use Brainbits\Period\YearPeriod;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PeriodExtension extends AbstractExtension
{
    public function __construct(private readonly PeriodFactory $periodFactory)
    {
    }

    /** @return TwigFunction[] */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('current_day_period', [$this, 'currentDayPeriod'], ['is_safe' => ['html']]),
            new TwigFunction('current_week_period', [$this, 'currentWeekPeriod'], ['is_safe' => ['html']]),
            new TwigFunction('current_month_period', [$this, 'currentMonthPeriod'], ['is_safe' => ['html']]),
            new TwigFunction('current_year_period', [$this, 'currentYearPeriod'], ['is_safe' => ['html']]),
            new TwigFunction(
                'current_running_week_period',
                [$this, 'currentRunningWeekPeriod'],
                ['is_safe' => ['html']],
            ),
            new TwigFunction(
                'current_running_month_period',
                [$this, 'currentRunningMonthPeriod'],
                ['is_safe' => ['html']],
            ),
            new TwigFunction(
                'current_running_year_period',
                [$this, 'currentRunningYearPeriod'],
                ['is_safe' => ['html']],
            ),
            new TwigFunction('current_period', [$this, 'currentPeriod'], ['is_safe' => ['html']]),
            new TwigFunction('next_period', [$this, 'nextPeriod'], ['is_safe' => ['html']]),
            new TwigFunction('previous_period', [$this, 'previousPeriod'], ['is_safe' => ['html']]),
            new TwigFunction('period_translation_key', [$this, 'periodTranslationKey'], ['is_safe' => ['html']]),
        ];
    }

    public function currentDayPeriod(): DayPeriod
    {
        return $this->periodFactory->currentDay();
    }

    public function currentWeekPeriod(): WeekPeriod
    {
        return $this->periodFactory->currentWeek();
    }

    public function currentMonthPeriod(): MonthPeriod
    {
        return $this->periodFactory->currentMonth();
    }

    public function currentYearPeriod(): YearPeriod
    {
        return $this->periodFactory->currentYear();
    }

    public function currentRunningWeekPeriod(): RunningWeekPeriod
    {
        return $this->periodFactory->currentRunningWeek();
    }

    public function currentRunningMonthPeriod(): RunningMonthPeriod
    {
        return $this->periodFactory->currentRunningMonth();
    }

    public function currentRunningYearPeriod(): RunningYearPeriod
    {
        return $this->periodFactory->currentRunningYear();
    }

    public function currentPeriod(Period $period): Period
    {
        return $this->periodFactory->current($period);
    }

    public function nextPeriod(Period $period): Period
    {
        return $this->periodFactory->next($period);
    }

    public function previousPeriod(Period $period): Period
    {
        return $this->periodFactory->previous($period);
    }

    public function periodTranslationKey(Period $period): string
    {
        return $this->periodFactory->getTranslationKey($period);
    }
}
