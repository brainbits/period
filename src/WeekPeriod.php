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
 * Week period
 */
final class WeekPeriod implements PeriodInterface
{
    /**
     * @var string
     */
    private $period;

    /**
     * @var DateTimeImmutable
     */
    private $startDate;

    /**
     * @var DateTimeImmutable
     */
    private $endDate;

    public function __construct(DateTimeImmutable $date)
    {
        $day = $date->format('N') - 1;

        $this->period = $date->format('o-W');
        $this->startDate = $date->modify("-$day day midnight");
        $this->endDate = $this->startDate->modify("+1 week");
    }

    public static function createFromPeriodString(string $period): self
    {
        if (!preg_match('/^\d\d\d\d-\d\d$/', $period, $match)) {
            throw new PeriodException("$period is not a valid week period string (e.g. 2017-42).");
        }

        list($year, $week) = explode('-', $period);

        return new self(new DateTimeImmutable(sprintf('%dW%02d', $year, $week)));
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
        return new self($this->getStartDate()->modify('+1 week'));
    }

    public function prev(): PeriodInterface
    {
        return new self($this->getStartDate()->modify('-1 week'));
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
            return 'period.week.this';
        }

        if ($current->next()->contains($this->getStartDate())) {
            return 'period.week.next';
        }

        if ($current->prev()->contains($this->getStartDate())) {
            return 'period.week.prev';
        }

        return 'period.week.period';
    }
}
