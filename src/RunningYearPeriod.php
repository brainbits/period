<?php

declare(strict_types = 1);

namespace Brainbits\Period;

use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use DateTimeInterface;

/**
 * Running year period
 */
final class RunningYearPeriod implements PeriodInterface
{
    private $period;

    private $startDate;

    private $endDate;

    public function __construct()
    {
        $this->startDate = new DateTimeImmutable("first day of january midnight");
        $this->endDate = new DateTimeImmutable('tomorrow midnight');
        $this->period = $this->startDate->format('Y');
    }

    public static function createCurrent(): self
    {
        return new self();
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
        return new YearPeriod($this->getStartDate()->modify('+1 year'));
    }

    public function prev(): PeriodInterface
    {
        return new YearPeriod($this->getStartDate()->modify('-1 year'));
    }

    public function now(): PeriodInterface
    {
        return new self();
    }

    public function getDateInterval(): DateInterval
    {
        return new DateInterval('P1Y');
    }

    public function getDatePeriod(DateInterval $interval, int $options = 0): DatePeriod
    {
        return new DatePeriod($this->startDate, $interval, $this->endDate, $options);
    }

    public function getTranslationKey(): string
    {
        return 'period.year.this';
    }
}
