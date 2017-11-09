<?php

namespace Ilios\ApiBundle\Normalizer;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Ilios\CoreBundle\Exception\InvalidInputWithSafeUserMessageException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Ilios\CoreBundle\Service\EntityMetadata;
use HTMLPurifier;

/**
 * Ilios Entity normalizer
 */
class Entity extends ObjectNormalizer
{
    /**
     * @var EntityMetadata
     */
    private $entityMetadata;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var HTMLPurifier
     */
    protected $purifier;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Set by the DI system.  We don't want to override
     * the constructor so this uses a setter to pass the needed
     * service
     *
     * @required
     * @param EntityMetadata $entityMetadata
     */
    public function setEntityMetadata(EntityMetadata $entityMetadata)
    {
        $this->entityMetadata = $entityMetadata;
    }

    /**
     * Set by the DI system.  We don't want to override
     * the constructor so this uses a setter to pass the needed
     * service
     *
     * @required
     * @param Registry $registry
     */
    public function setRegistry(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Set by the DI system.  We don't want to override
     * the constructor so this uses a setter to pass the needed
     * service
     *
     * @required
     * @param HTMLPurifier $purifier
     */
    public function setPurifier(HTMLPurifier $purifier)
    {
        $this->purifier = $purifier;
    }

    /**
     * Set by the DI system.  We don't want to override
     * the constructor so this uses a setter to pass the needed
     * service
     *
     * @required
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Overridden in order to filter out null values
     *
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $arr = parent::normalize($object, $format, $context);

        //remove null values
        return array_filter($arr, function ($value) {
            return null !== $value;
        });
    }

    /**
     * Using our annotation system convert raw entity values
     * into the data that will be seen in the JSON response
     *
     * {@inheritdoc}
     */
    protected function getAttributeValue($object, $property, $format = null, array $context = [])
    {
        $reflection = new \ReflectionClass($object);
        $exposedProperties = $this->entityMetadata->extractExposedProperties($reflection);
        if (!array_key_exists($property, $exposedProperties)) {
            return null;
        }
        $type = $this->entityMetadata->getTypeOfProperty($exposedProperties[$property]);

        if ($type === 'dateTime') {
            /** @var \DateTime $value */
            $value = $this->propertyAccessor->getValue($object, $property);
            if ($value) {
                return $value->format('c');
            }
        }

        if ($type === 'boolean') {
            $value = $this->propertyAccessor->getValue($object, $property);
            if ($value) {
                return boolval($value);
            }
        }
        if ($type === 'entity') {
            $entity = $this->propertyAccessor->getValue($object, $property);

            return $entity?(string) $entity:null;
        }

        if ($type === 'entityCollection') {
            $collection = $this->propertyAccessor->getValue($object, $property);

            $ids = $collection->map(function ($entity) {
                return $entity?(string) $entity:null;
            })->toArray();

            return array_values($ids);
        }

        return $this->propertyAccessor->getValue($object, $property);
    }

    /**
     * Takes data from user input and converts it back into what
     * our entities expect to be able to save
     *
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $reflection = new \ReflectionClass($class);
        $normalizedData = $this->prepareForDenormalization($data);
        $object = $this->instantiateObject($normalizedData, $class, $context, $reflection, false);
        $exposedProperties = $this->entityMetadata->extractExposedProperties($reflection);
        $writableProperties = $this->entityMetadata->extractWritableProperties($reflection);

        foreach ($normalizedData as $attribute => $value) {
            if ($this->nameConverter) {
                $attribute = $this->nameConverter->denormalize($attribute);
            }

            if (array_key_exists($attribute, $writableProperties)) {
                $denormalizedValue = $this->getDenormalizedValueForProperty(
                    $writableProperties[$attribute],
                    $value
                );
                try {
                    $this->setAttributeValue($object, $attribute, $denormalizedValue, $format, $context);
                } catch (\TypeError $exception) {
                    $type = $this->entityMetadata->getTypeOfProperty($writableProperties[$attribute]);
                    if (null !== $denormalizedValue or 'entity' !== $type) {
                        throw $exception;
                    }

                    // we ignore attempts to set entities to NULL when they are type hinted otherwise
                    // This will get caught in the validator with a much nicer message
                    $errorValue = null == $value?'null':$value;
                    $this->logger->error(
                        'Denormalization error ' . self::class . ' line ' . __LINE__ . ': ' .
                        "Unable to set '${attribute}' to '${errorValue}' on '${class}'.  Message: " .
                        $exception->getMessage()
                    );
                }
            } else {
                if (!array_key_exists($attribute, $exposedProperties)) {
                    throw new InvalidInputWithSafeUserMessageException(
                        sprintf("Extra data was sent:  '%s' is not a valid property", $attribute)
                    );
                }
            }
        }

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $this->entityMetadata->isAnIliosEntity($data);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $this->entityMetadata->isAnIliosEntity($type);
    }

    /**
     * Convert single API value back into what the entity needs
     *
     * @param \ReflectionProperty $property
     * @param mixed $value
     *
     * @todo could we extract the name of an Ilios Entity manager from the entity name?
     *          Then we could use that instead of the repository which would ensure anything
     *          weird that we are doing in the manager would work here
     *          [JJ 2/2017]
     * @return mixed
     */
    protected function getDenormalizedValueForProperty(\ReflectionProperty $property, $value)
    {
        $type = $this->entityMetadata->getTypeOfProperty($property);
        if (in_array($type, ['entity', 'entityCollection'])) {
            $manager = $this->registry->getManagerForClass($property->class);
            $metaData = $manager->getClassMetadata($property->class);
            $associationMappings = $metaData->associationMappings;

            $association = $associationMappings[$property->name];
            $targetEntity = $association['targetEntity'];
            $repository = $this->registry->getRepository($targetEntity);

            if ($type === 'entity') {
                if (null !== $value) {
                    $result = $repository->find($value);
                    if (!$result) {
                        $identifier = $metaData->getSingleIdentifierFieldName();
                        throw new InvalidInputWithSafeUserMessageException(
                            sprintf("Unable to resolve %s with %s %s", $property->getName(), $identifier, $value)
                        );
                    }
                    $value = $result;
                }
            } else {
                if (is_array($value) && !empty($value)) {
                    $result = $repository->findBy(['id' => $value]);
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

        if ($this->entityMetadata->isPropertyRemoveMarkup($property)) {
            $value = $this->purifier->purify($value);
        }

        return $value;
    }
}
