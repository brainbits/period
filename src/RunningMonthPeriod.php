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
use function trigger_error;

use const E_USER_DEPRECATED;

final class RunningMonthPeriod implements Period
{
    private string $period;
    private DateTimeImmutable $startDate;
    private DateTimeImmutable $endDate;

    public function __construct(DateTimeImmutable $date, DateTimeImmutable $now)
    {
        $this->period = $date->format('Y-m');
        $this->startDate = new DateTimeImmutable(sprintf('first day of %s midnight', $this->period));
        if ($date->format('Y-m') === $now->format('Y-m')) {
            $this->endDate = $now->modify('midnight +1 day -1 second');
        } else {
            $this->endDate = new DateTimeImmutable(sprintf(
                'first day of %s midnight +1 month -1 second',
                $this->period,
            ));
        }
    }

    /** @deprecated use PeriodFactory::currentRunningMonth() */
    public static function createCurrent(): self
    {
        trigger_error(__METHOD__ . ' is deprecated, use PeriodFactory::currentRunningMonth()', E_USER_DEPRECATED);

        $startDate = new DateTimeImmutable('first day of this month midnight');
        $endDate = new DateTimeImmutable('tomorrow midnight');

        return new self($startDate, $endDate);
    }

    public static function createFromPeriodString(string $period, DateTimeImmutable $now): self
    {
        if (!preg_match('/^(\d\d\d\d)-(\d\d)$/', $period, $match)) {
            throw InvalidPeriodString::invalidMonthPeriod($period);
        }

        [, $year, $month] = $match;

        return new self(new DateTimeImmutable(sprintf('%s-%s-01', $year, $month)), $now);
    }

    public static function createFromPeriodIdentifier(string $periodIdentifier, DateTimeImmutable $now): self
    {
        if (!str_starts_with($periodIdentifier, 'running-month#')) {
            throw InvalidPeriodIdentifier::invalidRunningMonthPeriodIdentifier($periodIdentifier);
        }

        return self::createFromPeriodString(substr($periodIdentifier, 14), $now);
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
        return 'running-month';
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

        return new MonthPeriod($this->getStartDate()->modify('+1 month'));
    }

    /** @deprecated use PeriodFactory::previous() */
    public function prev(): Period
    {
        trigger_error(__METHOD__ . ' is deprecated, use PeriodFactory::previous()', E_USER_DEPRECATED);

        return new MonthPeriod($this->getStartDate()->modify('-1 month'));
    }

    /** @deprecated use PeriodFactory::currentRunningMonth() */
    public function now(): self
    {
        trigger_error(__METHOD__ . ' is deprecated, use PeriodFactory::currentRunningMonth()', E_USER_DEPRECATED);

        return self::createCurrent();
    }

    public function getDateInterval(): DateInterval
    {
        return new DateInterval('P1M');
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
        return 'period.month.this';
    }
}
