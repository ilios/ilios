<?php

declare(strict_types=1);

namespace App\Denormalizer;

use App\Exception\InvalidInputWithSafeUserMessageException;
use App\Service\EntityRepositoryLookup;
use App\Service\EntityMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use ReflectionProperty;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use HTMLPurifier;
use TypeError;

/**
 * Denormalize Ilios Entities from JSON into Doctrine Entity Objects
 */
class EntityDenormalizer implements DenormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @var EntityMetadata
     */
    protected $entityMetadata;

    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var \HTMLPurifier
     */
    protected $purifier;

    /**
     * @var EntityRepositoryLookup
     */
    protected $entityManagerLookup;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        EntityMetadata $entityMetadata,
        ManagerRegistry $managerRegistry,
        HTMLPurifier $purifier,
        LoggerInterface $logger,
        EntityRepositoryLookup $entityManagerLookup
    ) {
        $this->entityMetadata = $entityMetadata;
        $this->managerRegistry = $managerRegistry;
        $this->purifier = $purifier;
        $this->entityManagerLookup = $entityManagerLookup;
        $this->logger = $logger;
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        if (array_key_exists('object_to_populate', $context)) {
            $entity = $context['object_to_populate'];
        } else {
            $entity = new $type();
        }

        $reflection = new \ReflectionClass($type);
        $writableProperties = $this->entityMetadata->extractWritableProperties($reflection);
        $readOnlyProperties = $this->entityMetadata->extractReadOnlyProperties($reflection);

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
                } catch (TypeError $exception) {
                    $type = $this->entityMetadata->getTypeOfProperty($property);
                    if (null !== $value or 'entity' !== $type) {
                        throw $exception;
                    }

                    // we ignore attempts to set entities to NULL when they are type hinted otherwise
                    // This will get caught in the validator with a much nicer message
                    $errorValue = null == $value ? 'null' : $value;
                    $this->logger->error(
                        'Denormalization error ' . self::class . ' line ' . __LINE__ . ': ' .
                        "Unable to set '${name}' to '${errorValue}' on '${type}'.  Message: " .
                        $exception->getMessage()
                    );
                }
            }
        }

        if (count($data)) {
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
     * @param mixed $value
     * @return mixed
     */
    protected function getDenormalizedValueForProperty(ReflectionProperty $property, $value)
    {
        $type = $this->entityMetadata->getTypeOfProperty($property);
        if (in_array($type, ['entity', 'entityCollection'])) {
            $entityManager = $this->managerRegistry->getManagerForClass($property->class);
            $metaData = $entityManager->getClassMetadata($property->class);
            $associationMappings = $metaData->associationMappings;

            $association = $associationMappings[$property->name];
            $targetEntity = $association['targetEntity'];

            $iliosManager = $this->entityManagerLookup->getManagerForEntity($targetEntity);

            if ($type === 'entity') {
                if (null !== $value) {
                    $result = $iliosManager->findOneById($value);
                    if (!$result) {
                        $identifier = $metaData->getSingleIdentifierFieldName();
                        throw new InvalidInputWithSafeUserMessageException(
                            sprintf("Unable to resolve %s with %s %s", $property->getName(), $identifier, $value)
                        );
                    }
                    $value = $result;
                }
            } elseif (is_array($value) && !empty($value)) {
                $result = $iliosManager->findBy(['id' => $value]);
                if (count($result) !== count($value)) {
                    $identifier = $metaData->getSingleIdentifierFieldName();
                    $method = 'get' . ucfirst($identifier);
                    $foundIds = array_map(function ($entity) use ($method) {
                        return $entity->$method();
                    }, $result);
                    $missingIds = array_filter($value, function ($id) use ($foundIds) {
                        return  !in_array($id, $foundIds);
                    });
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
            $defaultTimezone = new \DateTimeZone(date_default_timezone_get());
            $value = new \DateTime($value);
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

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $this->entityMetadata->isAnIliosEntity($type);
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
