<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Service\EntityMetadata;
use App\Attributes as IA;
use ArrayObject;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Ilios DTO normalizer
 */
class DTONormalizer implements NormalizerInterface
{
    public function __construct(protected EntityMetadata $entityMetadata)
    {
    }

    public function normalize(
        mixed $object,
        ?string $format = null,
        array $context = [],
    ): array|string|int|float|bool|ArrayObject|null {
        $reflection = new ReflectionClass($object);
        $exposedProperties = $this->entityMetadata->extractExposedProperties($reflection);

        $rhett = [];
        /** @var ReflectionProperty $property */
        foreach ($exposedProperties as $property) {
            $name = $property->getName();
            $value = $this->convertValueByType($property, $object->$name);
            if (!is_null($value)) {
                $rhett[$name] = $value;
            }
        }

        return $rhett;
    }

    /**
     * Converts value into the type dictated by it's annotation on the entity
     *
     * @param mixed $value
     */
    protected function convertValueByType(ReflectionProperty $property, $value): mixed
    {
        $type = $this->entityMetadata->getTypeOfProperty($property);
        if ($type === 'string') {
            return is_null($value) ? null : (string) $value;
        }

        if ($type === 'dateTime') {
            return is_null($value) ? null : $value->format('c');
        }

        if ($type === 'boolean') {
            return is_null($value) ? null : (bool) $value;
        }

        if ($type === 'integer') {
            return is_null($value) ? null : (int) $value;
        }

        if ($type === IA\Type::STRINGS || $type === IA\Type::INTEGERS) {
            $stringValues = array_map('strval', $value);
            return array_values($stringValues);
        }

        if ($type === IA\Type::DTOS) {
            return array_map([$this, 'normalize'], $value);
        }

        return $value;
    }

    /**
     * Check to see if we can normalize the object or class
     * {@inheritdoc}
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $format === 'json' && $this->entityMetadata->isAnIliosDto($data);
    }

    /**
     * Send *[null] to indicate we don't support anything by default
     * if it's a json request we will cache and support all the DTOs
     */
    public function getSupportedTypes(?string $format): array
    {
        $types = [
            '*' => null,
        ];

        if ($format === 'json') {
            foreach ($this->entityMetadata->getDtoList() as $name) {
                $types[$name] = true;
            }
        }


        return $types;
    }
}
