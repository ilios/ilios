<?php

declare(strict_types=1);

namespace App\Denormalizer;

use App\Exception\InvalidInputWithSafeUserMessageException;
use App\Service\EntityRepositoryLookup;
use App\Service\EntityMetadata;
use App\Attributes as IA;
use DateTime;
use DateTimeZone;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use HTMLPurifier;
use TypeError;

/**
 * Denormalize Ilios Entities from JSON into Doctrine Entity Objects
 */
class EntityDenormalizer implements DenormalizerInterface
{
    public function __construct(
        protected EntityMetadata $entityMetadata,
        protected ManagerRegistry $managerRegistry,
        protected HTMLPurifier $purifier,
        protected LoggerInterface $logger,
        protected EntityRepositoryLookup $entityRepositoryLookup
    ) {
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        if (array_key_exists('object_to_populate', $context)) {
            $entity = $context['object_to_populate'];
        } else {
            $entity = new $type();
        }

        $reflection = new ReflectionClass($type);
        $writableProperties = $this->entityMetadata->extractWritableProperties($reflection);
        $readOnlyProperties = $this->entityMetadata->extractOnlyReadableProperties($reflection);

        //remove all the read only properties from the input
        $readOnlyFields = array_column($readOnlyProperties, "name");
        $data = array_diff_key($data, array_fill_keys($readOnlyFields, null));

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        /** @var ReflectionProperty $property */
        foreach ($writableProperties as $property) {
            $name = $property->getName();
            if (array_key_exists($name, $data)) {
                $value = $this->getDenormalizedValueForProperty($property, $data[$name]);
                unset($data[$name]);
                try {
                    $propertyAccessor->setValue($entity, $name, $value);
                } catch (TypeError $exception) { /** @phpstan-ignore-line */
                    $type = $this->entityMetadata->getTypeOfProperty($property);
                    if (null !== $value or 'entity' !== $type) {
                        throw $exception;
                    }

                    // we ignore attempts to set entities to NULL when they are type hinted otherwise
                    // This will get caught in the validator with a much nicer message
                    $errorValue = null == $value ? 'null' : $value;
                    $this->logger->error(
                        'Denormalization error ' . self::class . ' line ' . __LINE__ . ': ' .
                        "Unable to set '{$name}' to '{$errorValue}' on '{$type}'.  Message: " .
                        $exception->getMessage()
                    );
                }
            }
        }

        if ($data !== []) {
            $extraFields = array_keys($data);
            $writableFields = array_column($writableProperties, "name");
            throw new InvalidInputWithSafeUserMessageException(
                sprintf(
                    'Recieved invalid input: %s. Only %s fields are allowed in %s.',
                    implode(',', $extraFields),
                    implode(',', $writableFields),
                    $type
                )
            );
        }

        return $entity;
    }

    /**
     * Convert single API value back into what the entity needs
     *
     */
    protected function getDenormalizedValueForProperty(ReflectionProperty $property, mixed $value): mixed
    {
        $type = $this->entityMetadata->getTypeOfProperty($property);
        if (in_array($type, ['entity', IA\Type::ENTITY_COLLECTION])) {
            $entityManager = $this->managerRegistry->getManagerForClass($property->class);
            $metaData = $entityManager->getClassMetadata($property->class);
            if (!$metaData->hasAssociation($property->name)) {
                throw new Exception("Invalid Association");
            }
            $targetEntity = $metaData->getAssociationTargetClass($property->name);
            $iliosManager = $this->entityRepositoryLookup->getManagerForEntity($targetEntity);

            if ($type === 'entity') {
                if (null !== $value) {
                    $result = $iliosManager->findOneById($value);
                    if (!$result) {
                        $identifiers = $metaData->getIdentifier();
                        $identifier = $identifiers[3] ?? 'No ID Found';
                        throw new InvalidInputWithSafeUserMessageException(
                            sprintf("Unable to resolve %s with %s %s", $property->getName(), $identifier, $value)
                        );
                    }
                    $value = $result;
                }
            } elseif (is_array($value) && !empty($value)) {
                $result = $iliosManager->findBy(['id' => $value]);
                if (count($result) !== count($value)) {
                    $identifiers = $metaData->getIdentifier();
                    if (!$identifiers[0]) {
                        throw new Exception("No Identifier found");
                    }
                    $identifier = $identifiers[0];
                    $method = 'get' . ucfirst($identifier);
                    $foundIds = array_map(fn($entity) => $entity->$method(), $result);
                    $missingIds = array_filter($value, fn($id) => !in_array($id, $foundIds));
                    throw new InvalidInputWithSafeUserMessageException(
                        sprintf(
                            "Unable to resolve %s[%s] for %s",
                            $identifier,
                            implode(',', $missingIds),
                            $property->getName()
                        )
                    );
                }
                $value = $result;
            } else {
                $value = [];
            }
        }

        if (null !== $value and $type === 'dateTime') {
            $defaultTimezone = new DateTimeZone(date_default_timezone_get());
            $value = new DateTime($value);
            $value->setTimezone($defaultTimezone);
        }

        if (null !== $value and $type === 'boolean') {
            $value = boolval($value);
        }

        if (null !== $value and $type === 'integer') {
            $value = intval($value);
        }

        if (null !== $value and $type === 'string') {
            $value = trim($value);
        }

        if (null !== $value and $this->entityMetadata->isPropertyRemoveMarkup($property)) {
            $value = $this->purifier->purify($value);
        }

        return $value;
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): bool {
        return $this->entityMetadata->isAnIliosEntity($type);
    }

    /**
     * The only things we denormalize are entities, for anything else *[null] tells
     * symfony to not even bother.
     */
    public function getSupportedTypes(?string $format): array
    {
        $types = [
            '*' => null,
        ];
        foreach ($this->entityMetadata->getEntityList() as $name) {
            $types[$name] = true;
        }

        return $types;
    }
}
