<?php

declare(strict_types=1);

namespace Brainbits\Period\Serializer\Normalizer;

use Brainbits\Period\DayPeriod;
use Brainbits\Period\MonthPeriod;
use Brainbits\Period\Period;
use Brainbits\Period\RangePeriod;
use Brainbits\Period\RunningMonthPeriod;
use Brainbits\Period\RunningWeekPeriod;
use Brainbits\Period\RunningYearPeriod;
use Brainbits\Period\WeekPeriod;
use Brainbits\Period\YearPeriod;
use DateTimeImmutable;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Throwable;

use function gettype;
use function is_string;
use function Safe\preg_match;
use function sprintf;

// phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint

/**
 * Normalizes an object implementing the {@see \Brainbits\Period\Period} to a period string.
 */
final class PeriodNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @param mixed   $object
     * @param string  $format
     * @param mixed[] $context
     *
     * @throws InvalidArgumentException
     */
    public function normalize($object, $format = null, array $context = []): string
    {
        if (!$object instanceof Period) {
            throw new InvalidArgumentException('The object must implement the "\Brainbits\Period\Period" interface.');
        }

        $period = $object->getPeriod();

        switch (true) {
            case $object instanceof DayPeriod:
                $type = 'day';
                break;
            case $object instanceof WeekPeriod:
                $type = 'week';
                break;
            case $object instanceof MonthPeriod:
                $type = 'month';
                break;
            case $object instanceof YearPeriod:
                $type = 'year';
                break;
            case $object instanceof RunningWeekPeriod:
                $type = 'runningweek';
                $period = 'current';
                break;
            case $object instanceof RunningMonthPeriod:
                $type = 'runningmonth';
                $period = 'current';
                break;
            case $object instanceof RunningYearPeriod:
                $type = 'runningyear';
                $period = 'current';
                break;
            case $object instanceof RangePeriod:
                $type = 'range';
                $period = sprintf(
                    '%s_%s',
                    $object->getStartDate()->format('Y-m-d'),
                    $object->getEndDate()->format('Y-m-d')
                );
                break;
            default:
                $type = 'unknown';
                $period = 'unknown';
        }

        return sprintf('period:%s:%s', $type, $period);
    }

    /**
     * @param mixed  $data
     * @param string $format
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Period;
    }

    /**
     * @param mixed   $data
     * @param string  $class
     * @param string  $format
     * @param mixed[] $context
     *
     * @throws NotNormalizableValueException
     */
    public function denormalize($data, $class, $format = null, array $context = []): Period
    {
        if (!is_string($data)) {
            throw new NotNormalizableValueException(sprintf('Data has to be a string, received %s', gettype($data)));
        }

        $type = '';
        $period = '';
        if (preg_match('/^period:(\w+):(.+)$/', $data, $match)) {
            $type = $match[1];
            $period = $match[2];
        }

        try {
            switch ($type) {
                case 'day':
                    return DayPeriod::createFromPeriodString($period);

                case 'week':
                    return WeekPeriod::createFromPeriodString($period);

                case 'month':
                    return MonthPeriod::createFromPeriodString($period);

                case 'year':
                    return YearPeriod::createFromPeriodString($period);

                case 'runningweek':
                    return RunningWeekPeriod::createCurrent();

                case 'runningmonth':
                    return RunningMonthPeriod::createCurrent();

                case 'runningyear':
                    return RunningYearPeriod::createCurrent();

                case 'range':
                    if (!preg_match('/^(\d{4}-\d{2}-\d{2})_(\d{4}-\d{2}-\d{2})$/', $period, $match)) {
                        throw new NotNormalizableValueException(sprintf(
                            'Not a valid range pattern: %s',
                            $period,
                        ));
                    }

                    $date1 = new DateTimeImmutable($match[1]);
                    $date2 = new DateTimeImmutable($match[2]);

                    return new RangePeriod($date1, $date2);
            }
        } catch (Throwable $e) {
            throw new NotNormalizableValueException($e->getMessage(), $e->getCode(), $e);
        }

        throw new NotNormalizableValueException(sprintf('Unknown type %s', $type));
    }

    /**
     * @param mixed  $data
     * @param string $type
     * @param string $format
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return is_string($data) && $type === Period::class;
    }
}
