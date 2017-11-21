<?php

declare(strict_types = 1);

namespace Brainbits\Period;

use Brainbits\Period\Exception\PeriodException;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use DateTimeInterface;
use Throwable;

/**
 * Month period
 */
class MonthPeriod implements PeriodInterface
{
    private $period;

    private $startDate;

    private $endDate;

    public function __construct(DateTimeImmutable $date)
    {
        $this->period = $date->format('Y-m');
        $this->startDate = new DateTimeImmutable("first day of {$this->period}");
        $this->endDate = new DateTimeImmutable("first day of {$this->period} +1 month");
    }

    public static function createFromPeriodString(string $period): self
    {
        if (!preg_match('/^\d\d\d\d-\d\d$/', $period, $match)) {
            throw new PeriodException("$period is not a valid month period string (e.g. 2017-12).");
        }

        list($year, $month) = explode('-', $period);

        return new self(new DateTimeImmutable("$year-$month-01"));
    }

    public static function createFromDateString(string $date): self
    {
        try {
            $period = new self(new DateTimeImmutable($date));
        } catch (Throwable $e) {
            throw new PeriodException("$date is not a valid date.");
        }

        return $period;
    }

    public static function createCurrent(): self
    {
        return new self(new DateTimeImmutable());
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
        return new self($this->getStartDate()->modify('+1 month'));
    }

    public function prev(): PeriodInterface
    {
        return new self($this->getStartDate()->modify('-1 month'));
    }

    public function now(): PeriodInterface
    {
        return self::createCurrent();
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
