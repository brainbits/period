<?php

declare(strict_types=1);

namespace Brainbits\Period\Exception;

use InvalidArgumentException;

use function sprintf;

final class InvalidPeriodString extends InvalidArgumentException implements PeriodException
{
    public static function invalidDayPeriod(string $period): self
    {
        return new self(sprintf('%s is not a valid day period string (e.g. 2017-12-24).', $period));
    }

    public static function invalidWeekPeriod(string $period): self
    {
        return new self(sprintf('%s is not a valid week period string (e.g. 2017-36).', $period));
    }

    public static function invalidMonthPeriod(string $period): self
    {
        return new self(sprintf('%s  is not a valid month period string (e.g. 2017-12).', $period));
    }

    public static function invalidYearPeriod(string $period): self
    {
        return new self(sprintf('%s  is not a valid year period string (e.g. 2017).', $period));
    }
}
