<?php

declare(strict_types=1);

namespace Brainbits\Period\Exception;

use Brainbits\Period\Period;
use InvalidArgumentException;

use function sprintf;

final class MismatchingPeriods extends InvalidArgumentException implements PeriodException
{
    public static function differ(Period $period, Period $otherPeriod): self
    {
        return new self(sprintf(
            'Both periods must have the same class, %s and %s given.',
            $period::class,
            $otherPeriod::class,
        ));
    }
}
