<?php

declare(strict_types=1);

namespace Brainbits\Period;

use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use DateTimeInterface;

use function Safe\sprintf;

final class RunningWeekPeriod implements Period, RunningPeriod
{
    private string $period;
    private DateTimeImmutable $startDate;
    private DateTimeImmutable $endDate;

    public function __construct()
    {
        $date = new DateTimeImmutable();
        $day = (int) $date->format('N') - 1;

        $this->period = $date->format('o-W');
        $this->startDate = $date->modify(sprintf('-%s day midnight', $day));
        $this->endDate = new DateTimeImmutable('tomorrow midnight');
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

    public function next(): Period
    {
        return new WeekPeriod($this->getStartDate()->modify('+1 week'));
    }

    public function prev(): Period
    {
        return new WeekPeriod($this->getStartDate()->modify('-1 week'));
    }

    public function now(): Period
    {
        return new self();
    }

    public function getDateInterval(): DateInterval
    {
        return new DateInterval('P1W');
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
        return 'period.week.this';
    }
}
