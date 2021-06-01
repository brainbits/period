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

use function Safe\preg_match;
use function Safe\sprintf;

final class YearPeriod implements Period
{
    private string $period;
    private DateTimeImmutable $startDate;
    private DateTimeImmutable $endDate;

    public function __construct(DateTimeInterface $date)
    {
        $this->period = $date->format('Y');
        $this->startDate = new DateTimeImmutable(sprintf('first day of january %s midnight', $this->period));
        $this->endDate = new DateTimeImmutable(sprintf('first day of january %s +1 year', $this->period));
    }

    public static function createFromPeriodString(string $period): self
    {
        if (!preg_match('/^\d\d{2,4}$/', $period, $match)) {
            throw InvalidPeriodString::invalidYearPeriod($period);
        }

        return new self(new DateTimeImmutable(sprintf('first day of january %s midnight', $period)));
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

    public static function createFromYear(int $year): self
    {
        return self::createFromPeriodString((string) $year);
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

    public function next(): Period
    {
        return new self($this->getStartDate()->modify('+1 year'));
    }

    public function prev(): Period
    {
        return new self($this->getStartDate()->modify('-1 year'));
    }

    public function now(): Period
    {
        return self::createCurrent();
    }

    public function getDateInterval(): DateInterval
    {
        return new DateInterval('P1Y');
    }

    /**
     * @return DatePeriod<DateTimeImmutable>
     */
    public function getDatePeriod(DateInterval $interval, int $options = 0): DatePeriod
    {
        return new DatePeriod($this->startDate, $interval, $this->endDate, $options);
    }

    public function getTranslationKey(): string
    {
        $current = $this->now();

        if ($current->contains($this->getStartDate())) {
            return 'period.year.this';
        }

        if ($current->next()->contains($this->getStartDate())) {
            return 'period.year.next';
        }

        if ($current->prev()->contains($this->getStartDate())) {
            return 'period.year.prev';
        }

        return 'period.year.period';
    }
}
