<?php

declare(strict_types=1);

namespace Brainbits\PeriodTest\Exception;

use Brainbits\Period\Exception\InvalidDateString;
use PHPUnit\Framework\TestCase;

final class InvalidDateStringTest extends TestCase
{
    public function testInvalidDate(): void
    {
        $this->assertSame(
            'foo-bar is not a valid day period string (e.g. 2017-12-24).',
            InvalidDateString::invalidDate('foo-bar')->getMessage(),
        );
    }
}
