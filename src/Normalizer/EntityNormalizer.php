<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Service\EntityMetadata;
use App\Attributes as IA;
use ArrayObject;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;
use HTMLPurifier;
use Psr\Log\LoggerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use ReflectionClass;
use ReflectionProperty;

/**
 * Ilios Entity normalizer
 */
class EntityNormalizer implements NormalizerInterface
{
    public function __construct(
        protected EntityMetadata $entityMetadata,
        protected ManagerRegistry $managerRegistry,
        protected HTMLPurifier $purifier,
        protected LoggerInterface $logger
    ) {
    }

    public function normalize(
        mixed $object,
        ?string $format = null,
        array $context = [],
    ): array|string|int|float|bool|ArrayObject|null {
        $reflection = new ReflectionClass($object);
        $exposedProperties = $this->entityMetadata->extractExposedProperties($reflection);
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $rhett = [];
        /** @var ReflectionProperty $property */
        foreach ($exposedProperties as $property) {
            $name = $property->getName();
            $value = $propertyAccessor->getValue($object, $name);
            if (!is_null($value)) {
                $rhett[$name] = $this->convertValueByType($property, $value);
            }
        }

        return $rhett;
    }

    /**
     * Converts value into the type dictated by it's annotation on the entity
     *
     */
    protected function convertValueByType(ReflectionProperty $property, mixed $value): mixed
    {
        $type = $this->entityMetadata->getTypeOfProperty($property);
        if ($type === 'dateTime') {
            if ($value) {
                return $value->format('c');
            }
        }

        if ($type === 'boolean') {
            if ($value) {
                return boolval($value);
            }
        }
        if ($type === 'entity') {
            return $value ? (string) $value : null;
        }

        if ($type === IA\Type::ENTITY_COLLECTION && $value instanceof Collection) {
            $ids = $value->map(fn($entity) => $entity ? (string) $entity : null)->toArray();

            return array_values($ids);
        }

        return $value;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $format === 'json' && $this->entityMetadata->isAnIliosEntity($data);
    }


    /**
     * Send *[null] to indicate we don't support anything by default
     * if it's a json-api request we will cache and support all the entities
     */
    public function getSupportedTypes(?string $format): array
    {
        $types = [
            '*' => null,
        ];
        if ($format === 'json') {
            foreach ($this->entityMetadata->getEntityList() as $name) {
                $types[$name] = true;
            }
        }

        return $types;
    }
}
