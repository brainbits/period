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
use function Safe\preg_match;
use function sprintf;

final class DayPeriod implements Period
{
    private string $period;
    private DateTimeImmutable $startDate;
    private DateTimeImmutable $endDate;

    public function __construct(DateTimeImmutable $date)
    {
        $this->period = $date->format('Y-m-d');
        $this->startDate = $date->modify('midnight');
        $this->endDate = $date->modify('+1 day midnight');
    }

    public static function createFromPeriodString(string $period): self
    {
        if (!preg_match('/^\d\d\d\d-\d\d-\d\d$/', $period, $match)) {
            throw InvalidPeriodString::invalidDayPeriod($period);
        }

        [$year, $month, $day] = explode('-', $period);

        return new self(new DateTimeImmutable(sprintf('%s-%s-%s', $year, $month, $day)));
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

    public function next(): Period
    {
        return new self($this->getStartDate()->modify('+1 day'));
    }

    public function prev(): Period
    {
        return new self($this->getStartDate()->modify('-1 day'));
    }

    public function now(): Period
    {
        return self::createCurrent();
    }

    public function getDateInterval(): DateInterval
    {
        return new DateInterval('P1D');
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
            return 'period.day.this';
        }

        if ($current->next()->contains($this->getStartDate())) {
            return 'period.day.next';
        }

        if ($current->prev()->contains($this->getStartDate())) {
            return 'period.day.prev';
        }

        return 'period.day.period';
    }
}
