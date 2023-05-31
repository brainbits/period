<?php

declare(strict_types=1);

namespace Brainbits\Period;

use Brainbits\Period\Exception\InvalidDateString;
use Brainbits\Period\Exception\InvalidPeriodIdentifier;
use Brainbits\Period\Exception\InvalidPeriodString;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use DateTimeInterface;
use Throwable;

use function Safe\preg_match;
use function sprintf;
use function str_starts_with;
use function substr;

final class YearPeriod implements Period
{
    private string $period;
    private DateTimeImmutable $startDate;
    private DateTimeImmutable $endDate;

    public function __construct(DateTimeInterface $date)
    {
        $this->period = $date->format('Y');
        $this->startDate = new DateTimeImmutable(sprintf('first day of january %s midnight', $this->period));
        $this->endDate = new DateTimeImmutable(sprintf('first day of january %s +1 year -1 second', $this->period));
    }

    public static function createFromPeriodString(string $period): self
    {
        if (!preg_match('/^\d\d{2,4}$/', $period, $match)) {
            throw InvalidPeriodString::invalidYearPeriod($period);
        }

        return new self(new DateTimeImmutable(sprintf('first day of january %s midnight', $period)));
    }

    public static function createFromPeriodIdentifier(string $periodIdentifier): self
    {
        if (!str_starts_with($periodIdentifier, 'year#')) {
            throw InvalidPeriodIdentifier::invalidYearPeriodIdentifier($periodIdentifier);
        }

        return self::createFromPeriodString(substr($periodIdentifier, 5));
    }

    public static function createFromDateString(string $date): self
    {
        try {
            $period = new self(new DateTimeImmutable($date));
        } catch (Throwable) {
            throw InvalidDateString::invalidDate($date);
        }

        return $period;
    }

    public static function createFromYear(int $year): self
    {
        return self::createFromPeriodString((string) $year);
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
        return 'year';
    }

    public function contains(DateTimeInterface $date): bool
    {
        return $this->getStartDate() <= $date && $date <= $this->getEndDate();
    }

    public function getDateInterval(): DateInterval
    {
        return new DateInterval('P1Y');
    }

    /** @return DatePeriod<DateTimeImmutable> */
    public function getDatePeriod(DateInterval $interval, int $options = 0): DatePeriod
    {
        return new DatePeriod($this->startDate, $interval, $this->endDate, $options);
    }
}
