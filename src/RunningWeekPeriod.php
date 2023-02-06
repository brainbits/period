<?php

declare(strict_types=1);

namespace Brainbits\Period;

use Brainbits\Period\Exception\InvalidPeriodIdentifier;
use Brainbits\Period\Exception\InvalidPeriodString;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use DateTimeInterface;

use function Safe\preg_match;
use function sprintf;
use function str_starts_with;
use function substr;

final class RunningWeekPeriod implements Period
{
    private string $period;
    private DateTimeImmutable $startDate;
    private DateTimeImmutable $endDate;

    public function __construct(DateTimeImmutable $date, DateTimeImmutable $now)
    {
        $day = (int) $date->format('N') - 1;

        $this->period = $date->format('o-W');
        $this->startDate = $date->modify(sprintf('-%s day midnight', $day));
        if ($date->format('o-W') === $now->format('o-W')) {
            $this->endDate = $now->modify('midnight +1 day -1 second');
        } else {
            $this->endDate = $date->modify(sprintf('-%s day midnight +1 week -1 second', $day));
        }
    }

    public static function createFromPeriodString(string $period, DateTimeImmutable $now): self
    {
        if (!preg_match('/^(\d\d\d\d)-(\d\d)$/', $period, $match)) {
            throw InvalidPeriodString::invalidWeekPeriod($period);
        }

        [, $year, $week] = $match;

        return new self(new DateTimeImmutable(sprintf('%dW%02d', $year, $week)), $now);
    }

    public static function createFromPeriodIdentifier(string $periodIdentifier, DateTimeImmutable $now): self
    {
        if (!str_starts_with($periodIdentifier, 'running-week#')) {
            throw InvalidPeriodIdentifier::invalidRunningWeekPeriodIdentifier($periodIdentifier);
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
        return 'running-week';
    }

    public function contains(DateTimeInterface $date): bool
    {
        return $this->getStartDate() <= $date && $date <= $this->getEndDate();
    }

    public function getDateInterval(): DateInterval
    {
        return new DateInterval('P1W');
    }

    /** @return DatePeriod<DateTimeImmutable> */
    public function getDatePeriod(DateInterval $interval, int $options = 0): DatePeriod
    {
        return new DatePeriod($this->startDate, $interval, $this->endDate, $options);
    }
}
