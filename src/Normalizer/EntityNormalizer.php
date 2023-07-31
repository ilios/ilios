<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Service\EntityMetadata;
use ArrayObject;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
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
        $object,
        string $format = null,
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
     * @param mixed $value
     */
    protected function convertValueByType(ReflectionProperty $property, $value): mixed
    {
        $type = $this->entityMetadata->getTypeOfProperty($property);
        if ($type === 'dateTime') {
            /** @var DateTime $value */
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

        if ($type === 'entityCollection') {
            /** @var ArrayCollection $value $ids */
            $ids = $value->map(fn($entity) => $entity ? (string) $entity : null)->toArray();

            return array_values($ids);
        }

        return $value;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $format === 'json' && $this->entityMetadata->isAnIliosEntity($data);
    }


    public function getSupportedTypes(?string $format): array
    {
        $types = [];
        foreach ($this->entityMetadata->getEntityList() as $name) {
            $types[$name] = true;
        }

        return $types;
    }
}
