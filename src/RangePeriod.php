<?php

declare(strict_types=1);

namespace Brainbits\Period;

use Brainbits\Period\Exception\InvalidPeriodIdentifier;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use DateTimeInterface;

use function Safe\preg_match;
use function sprintf;
use function trigger_error;

use const E_USER_DEPRECATED;

final class RangePeriod implements Period
{
    private string $period;
    private DateTimeImmutable $startDate;
    private DateTimeImmutable $endDate;

    public function __construct(DateTimeImmutable $startDate, DateTimeImmutable $endDate)
    {
        $this->startDate = $startDate->modify('midnight');
        $this->endDate = $endDate->modify('midnight +1 day -1 second');
        $this->period = $startDate->format('Y-m-d') . ' - ' . $endDate->format('Y-m-d');
    }

    public static function createFromPeriodIdentifier(string $periodIdentifier): self
    {
        if (!preg_match('/^range#(\d\d\d\d)-(\d\d)-(\d\d)#(\d\d\d\d)-(\d\d)-(\d\d)$/', $periodIdentifier, $match)) {
            throw InvalidPeriodIdentifier::invalidRangePeriodIdentifier($periodIdentifier);
        }

        [, $startYear, $startMonth, $startDay, $endYear, $endMonth, $endDay] = $match;

        return new self(
            new DateTimeImmutable(sprintf('%s-%s-%s', $startYear, $startMonth, $startDay)),
            new DateTimeImmutable(sprintf('%s-%s-%s', $endYear, $endMonth, $endDay)),
        );
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
        return sprintf(
            '%s#%s#%s',
            $this->getPeriodPrefix(),
            $this->startDate->format('Y-m-d'),
            $this->endDate->format('Y-m-d'),
        );
    }

    public function getPeriodPrefix(): string
    {
        return 'range';
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

        return new static($this->getEndDate(), $this->getEndDate()->add($this->getDateInterval()));
    }

    /** @deprecated use PeriodFactory::previous() */
    public function prev(): Period
    {
        trigger_error(__METHOD__ . ' is deprecated, use PeriodFactory::previous()', E_USER_DEPRECATED);

        return new static($this->getStartDate()->sub($this->getDateInterval()), $this->getStartDate());
    }

    /** @deprecated */
    public function now(): Period
    {
        trigger_error(__METHOD__ . ' is deprecated, there is noe replacing method', E_USER_DEPRECATED);

        $today = new DateTimeImmutable();

        return new self($today, $today->add($this->getDateInterval()));
    }

    public function getDateInterval(): DateInterval
    {
        return $this->startDate->diff($this->endDate->modify('+1 second'));
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
        return 'period.range.period';
    }
}
