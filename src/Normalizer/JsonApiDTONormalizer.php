<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Service\EntityMetadata;
use App\Attributes as IA;
use ArrayObject;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use ReflectionClass;
use ReflectionProperty;

class JsonApiDTONormalizer implements NormalizerInterface
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

    protected function getPropertyValue(ReflectionProperty $property, object $object): mixed
    {
        $type = $this->entityMetadata->getTypeOfProperty($property);
        if ($type === 'string') {
            $value = $object->{$property->name};
            return null === $value ? null : (string) $value;
        }

        if ($type === 'dateTime') {
            $value = $object->{$property->name};
            return null === $value ? null : $value->format('c');
        }

        if ($type === IA\Type::STRINGS || $type === IA\Type::INTEGERS) {
            $values = $object->{$property->name};
            $stringValues = array_map('strval', $values);

            return array_values($stringValues);
        }

        return $object->{$property->name};
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $format === 'json-api' && $this->entityMetadata->isAnIliosDto($data);
    }

    /**
     * Send *[null] to indicate we don't support anything by default
     * if it's a json-api request we will cache and support all the DTOs
     */
    public function getSupportedTypes(?string $format): array
    {
        $types = [
            '*' => null,
        ];

        if ($format === 'json-api') {
            foreach ($this->entityMetadata->getDtoList() as $name) {
                $types[$name] = true;
            }
        }


        return $types;
    }
}
