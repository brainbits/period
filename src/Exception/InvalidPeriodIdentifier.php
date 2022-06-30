<?php

declare(strict_types=1);

namespace Brainbits\Period\Exception;

use Brainbits\Period\Period;
use InvalidArgumentException;

use function sprintf;

final class InvalidPeriodIdentifier extends InvalidArgumentException implements PeriodException
{
    public static function unknownPeriodIdentifier(string $periodIdentifier): self
    {
        return new self(sprintf('%s is not a valid period identifier.', $periodIdentifier));
    }

    public static function unknownPeriod(Period $period): self
    {
        return new self(sprintf('%s is not a valid period.', $period::class));
    }

    public static function invalidDayPeriodIdentifier(string $periodIdentifier): self
    {
        return new self(sprintf('%s is not a valid period identifier (e.g. day#2017-12-24).', $periodIdentifier));
    }

    public static function invalidWeekPeriodIdentifier(string $periodIdentifier): self
    {
        return new self(sprintf('%s is not a valid week period identifier (e.g. week#2017-36).', $periodIdentifier));
    }

    public static function invalidRunningWeekPeriodIdentifier(string $periodIdentifier): self
    {
        return new self(sprintf(
            '%s is not a valid running week period identifier (e.g. running-week#2017-36).',
            $periodIdentifier,
        ));
    }

    public static function invalidMonthPeriodIdentifier(string $periodIdentifier): self
    {
        return new self(sprintf('%s is not a valid month period identifier (e.g. month#2017-12).', $periodIdentifier));
    }

    public static function invalidRunningMonthPeriodIdentifier(string $periodIdentifier): self
    {
        return new self(sprintf(
            '%s is not a valid running month period identifier (e.g. running-month#2017-12).',
            $periodIdentifier,
        ));
    }

    public static function invalidYearPeriodIdentifier(string $periodIdentifier): self
    {
        return new self(sprintf('%s is not a valid year period identifier (e.g. year#2017).', $periodIdentifier));
    }

    public static function invalidRunningYearPeriodIdentifier(string $periodIdentifier): self
    {
        return new self(sprintf(
            '%s is not a valid running year period identifier (e.g. running-year#2017).',
            $periodIdentifier,
        ));
    }

    public static function invalidRangePeriodIdentifier(string $periodIdentifier): self
    {
        return new self(sprintf(
            '%s is not a valid range period identifier (e.g. range#2017-12-24#2017-12-26).',
            $periodIdentifier,
        ));
    }
}
