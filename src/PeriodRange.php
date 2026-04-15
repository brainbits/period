<?php

declare(strict_types=1);

namespace Brainbits\Period;

use Brainbits\Period\Exception\MismatchingPeriods;
use IteratorAggregate;
use Traversable;

use function array_first;
use function array_last;
use function array_values;

/** @implements IteratorAggregate<Period> */
final class PeriodRange implements IteratorAggregate
{
    /** @var non-empty-list<Period> */
    private array $periods;

    public function __construct(Period ...$periods)
    {
        if (!$periods) {
            throw MismatchingPeriods::empty();
        }

        $firstPeriod = null;
        foreach ($periods as $period) {
            if ($firstPeriod === null) {
                $firstPeriod = $period;
                continue;
            }

            if ($firstPeriod::class !== $period::class) {
                throw MismatchingPeriods::differ($firstPeriod, $period);
            }
        }

        $this->periods = array_values($periods);
    }

    public function startPeriod(): Period
    {
        return array_first($this->periods);
    }

    public function endPeriod(): Period
    {
        return array_last($this->periods);
    }

    /** @return Traversable<Period> */
    public function getIterator(): Traversable
    {
        yield from $this->periods;
    }
}
