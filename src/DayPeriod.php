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
use function trigger_error;

use const E_USER_DEPRECATED;

final class DayPeriod implements Period
{
    private string $period;
    private DateTimeImmutable $startDate;
    private DateTimeImmutable $endDate;

    public function __construct(DateTimeImmutable $date)
    {
        $this->period = $date->format('Y-m-d');
        $this->startDate = $date->modify('midnight');
        $this->endDate = $date->modify('+1 day midnight -1 second');
    }

    /** @deprecated use PeriodFactory::currentDay() */
    public static function createCurrent(): self
    {
        trigger_error(__METHOD__ . ' is deprecated, use PeriodFactory::currentDay()', E_USER_DEPRECATED);

        return new self(new DateTimeImmutable());
    }

    public static function createFromPeriodString(string $period): self
    {
        if (!preg_match('/^(\d\d\d\d)-(\d\d)-(\d\d)$/', $period, $match)) {
            throw InvalidPeriodString::invalidDayPeriod($period);
        }

        [, $year, $month, $day] = $match;

        return new self(new DateTimeImmutable(sprintf('%s-%s-%s', $year, $month, $day)));
    }

    public static function createFromPeriodIdentifier(string $periodIdentifier): self
    {
        if (!str_starts_with($periodIdentifier, 'day#')) {
            throw InvalidPeriodIdentifier::invalidDayPeriodIdentifier($periodIdentifier);
        }

        return self::createFromPeriodString(substr($periodIdentifier, 4));
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

    public function getStartDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTimeImmutable
    {
        return $this->endDate;
    }

    /** @deprecated use getPeriodString() */
    public function getPeriod(): string
    {
        trigger_error(__METHOD__ . ' is deprecated, use getPeriodString()', E_USER_DEPRECATED);

        return $this->getPeriodString();
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
        return 'day';
    }

    public function contains(DateTimeInterface $date): bool
    {
        return $this->getStartDate() <= $date && $date <= $this->getEndDate();
    }

    /** @deprecated use PeriodFactory::isCurrent() */
    public function isCurrent(): bool
    {
        trigger_error(__METHOD__ . ' is deprecated, use PeriodFactory::isCurrent()', E_USER_DEPRECATED);

        return $this->contains(new DateTimeImmutable());
    }

    /** @deprecated use PeriodFactory::next() */
    public function next(): Period
    {
        trigger_error(__METHOD__ . ' is deprecated, use PeriodFactory::next()', E_USER_DEPRECATED);

        return new self($this->getStartDate()->modify('+1 day'));
    }

    /** @deprecated use PeriodFactory::previous() */
    public function prev(): Period
    {
        trigger_error(__METHOD__ . ' is deprecated, use PeriodFactory::previous()', E_USER_DEPRECATED);

        return new self($this->getStartDate()->modify('-1 day'));
    }

    /** @deprecated use PeriodFactory::currentDay() */
    public function now(): self
    {
        trigger_error(__METHOD__ . ' is deprecated, use PeriodFactory::currentDay()', E_USER_DEPRECATED);

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

    /** @deprecated use PeriodFactory::getTranslationKey() */
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
