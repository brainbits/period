<?php

declare(strict_types = 1);

namespace Brainbits\Period;

use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use DateTimeInterface;

/**
 * Month period
 */
final class RunningMonthPeriod implements PeriodInterface
{
    private $period;

    private $startDate;

    private $endDate;

    public function __construct()
    {
        $this->startDate = new DateTimeImmutable('first day of this month');
        $this->endDate = new DateTimeImmutable('tomorrow midnight');
        $this->period = $this->startDate->format('Y-m');
    }

    public function getStartDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTimeImmutable
    {
        return $this->endDate->modify('-1 second');
    }

    public function getPeriod(): string
    {
        return $this->period;
    }

    public function contains(DateTimeInterface $date): bool
    {
        return $this->getStartDate() <= $date && $date <= $this->getEndDate();
    }

    public function isCurrent(): bool
    {
        return $this->contains(new DateTimeImmutable());
    }

    public function next(): PeriodInterface
    {
        return new MonthPeriod($this->getStartDate()->modify('+1 month'));
    }

    public function prev(): PeriodInterface
    {
        return new MonthPeriod($this->getStartDate()->modify('-1 month'));
    }

    public function now(): PeriodInterface
    {
        return new self();
    }

    public function getDateInterval(): DateInterval
    {
        return $this->startDate->diff($this->endDate);
    }

    public function getDatePeriod(DateInterval $interval, int $options = 0): DatePeriod
    {
        return new DatePeriod($this->startDate, $interval, $this->endDate, $options);
    }

    public function getTranslationKey(): string
    {
        $current = $this->now();

        if ($current->contains($this->getStartDate())) {
            return 'period.month.this';
        }

        if ($current->next()->contains($this->getStartDate())) {
            return 'period.month.next';
        }

        if ($current->prev()->contains($this->getStartDate())) {
            return 'period.month.prev';
        }

        return 'period.month.period';
    }
}
