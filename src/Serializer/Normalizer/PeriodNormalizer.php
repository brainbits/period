<?php

declare(strict_types=1);

namespace Brainbits\Period\Serializer\Normalizer;

use Brainbits\Period\Exception\PeriodException;
use Brainbits\Period\Period;
use Brainbits\Period\PeriodFactory;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function gettype;
use function is_string;
use function sprintf;

// phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint

/**
 * Normalizes an object implementing the {@see \Brainbits\Period\Period} to a period string.
 */
final class PeriodNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function __construct(private readonly PeriodFactory $periodFactory)
    {
    }

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
            throw new InvalidArgumentException(sprintf('The object must implement the "%s" interface.', Period::class));
        }

        return $object->getPeriodIdentifier();
    }

    /**
     * @param mixed   $data
     * @param string  $format
     * @param mixed[] $context
     */
    public function supportsNormalization($data, $format = null, array $context = []): bool
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

        try {
            return $this->periodFactory->fromIdentifier($data);
        } catch (PeriodException $e) {
            throw new NotNormalizableValueException($e->getMessage(), 0, $e);
        }
    }

    /**
     * @param mixed   $data
     * @param string  $type
     * @param string  $format
     * @param mixed[] $context
     */
    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return is_string($data) && $type === Period::class;
    }
}
