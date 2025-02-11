<?php

declare(strict_types=1);

namespace Brainbits\Period;

use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use DateTimeInterface;

interface Period
{
    public function getStartDate(): DateTimeImmutable;

    public function getEndDate(): DateTimeImmutable;

    public function getPeriodString(): string;

    public function getPeriodIdentifier(): string;

    public function getPeriodPrefix(): string;

    public function contains(DateTimeInterface $date): bool;

    public function getDateInterval(): DateInterval;

    /**
     * Allows iteration over a set of dates and times,
     * recurring at regular intervals, over the Period object.
     *
     * $options can be set to DatePeriod::EXCLUDE_START_DATE to exclude
     * the start date from the set of recurring dates within the period.
     *
     * @return DatePeriod<DateTimeImmutable, DateTimeImmutable, null>
     */
    public function getDatePeriod(DateInterval $interval, int $options = 0): DatePeriod;
}
