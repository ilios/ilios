<?php

namespace Ilios\ApiBundle\Normalizer;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Ilios\CoreBundle\Service\EntityMetadata;

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
     * @param EntityMetadata $entityMetadata
     */
    public function setEntityMetadata(EntityMetadata $entityMetadata)
    {
        $this->entityMetadata = $entityMetadata;
    }

    /**
     * @param Registry $registry
     */
    public function setRegistry(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $arr = parent::normalize($object, $format, $context);

        //remove null values
        return array_filter($arr, function ($value) {
            return null !== $value;
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttributeValue($object, $property, $format = null, array $context = array())
    {
        $reflection = new \ReflectionClass($object);
        $exposedProperties = $this->entityMetadata->extractExposedProperties($reflection);
        if (!array_key_exists($property, $exposedProperties)) {
            return null;
        }
        $type = $this->entityMetadata->getTypeOfProperty($exposedProperties[$property]);

        if ($type === 'dateTime') {
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

    public function denormalize($data, $class, $format = null, array $context = array())
    {
        $reflection = new \ReflectionClass($class);
        $normalizedData = $this->prepareForDenormalization($data);
        $object = $this->instantiateObject($normalizedData, $class, $context, $reflection, false);
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
                } catch (\InvalidArgumentException $exception) {
                    $type = $this->entityMetadata->getTypeOfProperty($writableProperties[$attribute]);
                    if (null !== $denormalizedValue or 'entity' !== $type) {
                        throw $exception;
                    }

                    // we ignore attempts to set entities to NULL when they are type hinted otherwise
                    // This will get caught in the validator with a much nicer message
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
     * Convert API data back into what the entity needs
     *
     * @param \ReflectionProperty $property
     * @param mixed $value
     *
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

            if (!empty($value) && $type === 'entity') {
                $value = $repository->find($value);
            } else {
                if (is_array($value) && !empty($value)) {
                    $value = $repository->findBy(['id' => $value]);
                } else {
                    $value = [];
                }
            }
        }

        if (null !== $value and $type === 'dateTime') {
            $value = new \DateTime($value);
        }

        if (null !== $value and $type === 'boolean') {
            $value = boolval($value);
        }

        return $value;
    }
}
