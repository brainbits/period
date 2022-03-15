<?php

declare(strict_types=1);

namespace Brainbits\Period\Exception;

use InvalidArgumentException;

use function sprintf;

final class InvalidDateString extends InvalidArgumentException implements PeriodException
{
    public static function invalidDate(string $date): self
    {
        return new self(sprintf('%s is not a valid day period string (e.g. 2017-12-24).', $date));
    }
}
