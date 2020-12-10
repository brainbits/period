<?php

declare(strict_types=1);

namespace Brainbits\Period;

use Brainbits\Period\Exception\InvalidDateString;
use Brainbits\Period\Exception\InvalidPeriodString;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use DateTimeInterface;
use Throwable;

use function explode;
use function preg_match;
use function sprintf;

/**
 * Month period
 */
final class MonthPeriod implements PeriodInterface
{
    private string $period;
    private DateTimeImmutable $startDate;
    private DateTimeImmutable $endDate;

    public function __construct(DateTimeImmutable $date)
    {
        $this->period = $date->format('Y-m');
        $this->startDate = new DateTimeImmutable(sprintf('first day of %s', $this->period));
        $this->endDate = new DateTimeImmutable(sprintf('first day of %s +1 month', $this->period));
    }

    public static function createFromPeriodString(string $period): self
    {
        if (!preg_match('/^\d\d\d\d-\d\d$/', $period, $match)) {
            throw InvalidPeriodString::invalidMonthPeriod($period);
        }

        [$year, $month] = explode('-', $period);

        return new self(new DateTimeImmutable(sprintf('%s-%s-01', $year, $month)));
    }

    public static function createFromDateString(string $date): self
    {
        try {
            $period = new self(new DateTimeImmutable($date));
        } catch (Throwable $e) {
            throw InvalidDateString::invalidDate($date);
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
        return new DateInterval('P1M');
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
