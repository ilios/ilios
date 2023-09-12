<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Service\EntityMetadata;
use ArrayObject;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use ReflectionClass;
use ReflectionProperty;
use DateTime;

class JsonApiDTONormalizer implements NormalizerInterface
{
    public function __construct(protected EntityMetadata $entityMetadata)
    {
    }

    public function normalize(
        $object,
        string $format = null,
        array $context = [],
    ): array|string|int|float|bool|ArrayObject|null {
        $reflection = new ReflectionClass($object);
        $exposedProperties = $this->entityMetadata->extractExposedProperties($reflection);
        $attributes = [];
        foreach ($exposedProperties as $property) {
            $attributes[$property->name] = $this->getPropertyValue($property, $object);
        }

        $relatedProperties = $this->entityMetadata->extractRelated($reflection);

        $type = $this->entityMetadata->extractType($reflection);

        $idProperty = $this->entityMetadata->extractId($reflection);
        $id = $attributes[$idProperty];

        $related = [];
        foreach ($relatedProperties as $attributeName => $relationshipType) {
            $value = $attributes[$attributeName];
            $related[$attributeName] = [
                'type' => $relationshipType,
                'value' => $value,
            ];
            unset($attributes[$attributeName]);
        }
        unset($attributes[$idProperty]);

        return [
            'id' => $id,
            'type' => $type,
            'attributes' => $attributes,
            'related' => $related,
        ];
    }

    protected function getPropertyValue(ReflectionProperty $property, object $object)
    {
        $type = $this->entityMetadata->getTypeOfProperty($property);
        if ($type === 'string') {
            $value = $object->{$property->name};
            return null === $value ? null : (string) $value;
        }

        if ($type === 'dateTime') {
            /** @var DateTime $value */
            $value = $object->{$property->name};
            return null === $value ? null : $value->format('c');
        }

        if ($type === 'array<string>' || $type === 'array<integer>') {
            $values = $object->{$property->name};
            $stringValues = array_map('strval', $values);

            return array_values($stringValues);
        }

        return $object->{$property->name};
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $format === 'json-api' && $this->entityMetadata->isAnIliosDto($data);
    }
}
