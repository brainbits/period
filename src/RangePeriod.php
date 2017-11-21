<?php

declare(strict_types = 1);

namespace Brainbits\Period;

use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use DateTimeInterface;

/**
 * Range period
 */
class RangePeriod implements PeriodInterface
{
    /**
     * @var string
     */
    private $period;

    private $startDate;

    private $endDate;

    public function __construct(DateTimeImmutable $startDate, DateTimeImmutable $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->period = $startDate->format('Y-m-d').' - '.$endDate->format('Y-m-d');
    }

    public function getStartDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTimeImmutable
    {
        return $this->endDate;
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
        return new static($this->getEndDate(), $this->getEndDate()->add($this->getDateInterval()));
    }

    public function prev(): PeriodInterface
    {
        return new static($this->getStartDate()->sub($this->getDateInterval()), $this->getStartDate());
    }

    public function now(): PeriodInterface
    {
        $today = new DateTimeImmutable();

        return new self($today, $today->add($this->getDateInterval()));
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
        return 'period.range.period';
    }
}
