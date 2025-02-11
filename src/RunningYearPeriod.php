<?php

declare(strict_types=1);

namespace Brainbits\Period;

use Brainbits\Period\Exception\InvalidPeriodIdentifier;
use Brainbits\Period\Exception\InvalidPeriodString;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use DateTimeInterface;

use function preg_match;
use function sprintf;
use function str_starts_with;
use function substr;

final class RunningYearPeriod implements Period
{
    private string $period;
    private DateTimeImmutable $startDate;
    private DateTimeImmutable $endDate;

    public function __construct(DateTimeImmutable $date, DateTimeImmutable $now)
    {
        $this->period = $date->format('Y');
        $this->startDate = new DateTimeImmutable(sprintf('first day of january %s midnight', $this->period));
        if ($date->format('Y') === $now->format('Y')) {
            $this->endDate = $now->modify('midnight +1 day -1 second');
        } else {
            $this->endDate = new DateTimeImmutable(sprintf('first day of january %s +1 year -1 second', $this->period));
        }
    }

    public static function createFromPeriodString(string $period, DateTimeImmutable $now): self
    {
        if (!preg_match('/^\d\d{2,4}$/', $period)) {
            throw InvalidPeriodString::invalidYearPeriod($period);
        }

        return new self(new DateTimeImmutable(sprintf('first day of january %s midnight', $period)), $now);
    }

    public static function createFromPeriodIdentifier(string $periodIdentifier, DateTimeImmutable $now): self
    {
        if (!str_starts_with($periodIdentifier, 'running-year#')) {
            throw InvalidPeriodIdentifier::invalidRunningYearPeriodIdentifier($periodIdentifier);
        }

        return self::createFromPeriodString(substr($periodIdentifier, 13), $now);
    }

    public function getStartDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTimeImmutable
    {
        return $this->endDate;
    }

    public function getPeriodString(): string
    {
        return $this->period;
    }

    public function getPeriodIdentifier(): string
    {
        return sprintf('%s#%s', $this->getPeriodPrefix(), $this->period);
    }

    public function getPeriodPrefix(): string
    {
        return 'running-year';
    }

    public function contains(DateTimeInterface $date): bool
    {
        return $this->getStartDate() <= $date && $date <= $this->getEndDate();
    }

    public function getDateInterval(): DateInterval
    {
        return new DateInterval('P1Y');
    }

    /** @return DatePeriod<DateTimeImmutable, DateTimeImmutable, null> */
    public function getDatePeriod(DateInterval $interval, int $options = 0): DatePeriod
    {
        return new DatePeriod($this->startDate, $interval, $this->endDate, $options);
    }
}
