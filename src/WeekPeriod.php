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

use function preg_match;
use function sprintf;
use function str_starts_with;
use function substr;

final class WeekPeriod implements Period
{
    private string $period;
    private DateTimeImmutable $startDate;
    private DateTimeImmutable $endDate;

    public function __construct(DateTimeImmutable $date)
    {
        $day = (int) $date->format('N') - 1;

        $this->period = $date->format('o-W');
        $this->startDate = $date->modify(sprintf('-%s day midnight', $day));
        $this->endDate = $this->startDate->modify('+1 week -1 second');
    }

    public static function createFromPeriodString(string $period): self
    {
        if (!preg_match('/^(\d\d\d\d)-(\d\d)$/', $period, $match)) {
            throw InvalidPeriodString::invalidWeekPeriod($period);
        }

        [, $year, $week] = $match;

        return new self(new DateTimeImmutable(sprintf('%dW%02d', $year, $week)));
    }

    public static function createFromPeriodIdentifier(string $periodIdentifier): self
    {
        if (!str_starts_with($periodIdentifier, 'week#')) {
            throw InvalidPeriodIdentifier::invalidWeekPeriodIdentifier($periodIdentifier);
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
        return 'week';
    }

    public function contains(DateTimeInterface $date): bool
    {
        return $this->getStartDate() <= $date && $date <= $this->getEndDate();
    }

    public function getDateInterval(): DateInterval
    {
        return new DateInterval('P1W');
    }

    /** @return DatePeriod<DateTimeImmutable, DateTimeImmutable, null> */
    public function getDatePeriod(DateInterval $interval, int $options = 0): DatePeriod
    {
        return new DatePeriod($this->startDate, $interval, $this->endDate, $options);
    }
}
