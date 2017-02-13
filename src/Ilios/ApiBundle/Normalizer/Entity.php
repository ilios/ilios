<?php

namespace Ilios\ApiBundle\Normalizer;

use Ilios\ApiBundle\Annotation\Type;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Ilios Entity normalizer
 */
class Entity extends ObjectNormalizer
{
    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @param Reader $annotationReader
     */
    public function setReader(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
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
        return array_filter($arr, function($value) {
            return null !== $value;
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttributeValue($object, $property, $format = null, array $context = array())
    {
        $reflection = new \ReflectionClass($object);
        $exposedProperties = $this->extractExposedProperties($reflection);
        if (!array_key_exists($property, $exposedProperties)) {
            return null;
        }
        $type = $this->getTypeOfProperty($exposedProperties[$property]);

        if ($type === 'dateTime') {
            $value = $this->propertyAccessor->getValue($object, $property);
            if ($value) {
                return $value->format('c');
            }
        }
        if ($type === 'entity') {
            $entity = $this->propertyAccessor->getValue($object, $property);

            return $entity?(string) $entity:null;
        }

        if ($type === 'entityCollection') {
            $collection = $this->propertyAccessor->getValue($object, $property);

            $ids = $collection->map(function ($entity){
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
        $exposedProperties = $this->extractExposedProperties($reflection);

        foreach ($normalizedData as $attribute => $value) {
            if ($this->nameConverter) {
                $attribute = $this->nameConverter->denormalize($attribute);
            }

            if (!empty($value) && array_key_exists($attribute, $exposedProperties)) {
                $denormalizedValue = $this->getDenormalizedValueForProperty(
                    $exposedProperties[$attribute],
                    $value
                );
                if (null !== $denormalizedValue) {
                    $this->setAttributeValue($object, $attribute, $denormalizedValue, $format, $context);
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
        return $this->isAnIliosEntity($data);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $this->isAnIliosEntity($type);
    }

    protected function isAnIliosEntity($classNameOrObject)
    {
        if (
            (is_string($classNameOrObject) && class_exists($classNameOrObject)) ||
            is_object($classNameOrObject)
        ) {
            $annotation = $this->annotationReader->getClassAnnotation(
                new \ReflectionClass($classNameOrObject),
                'Ilios\ApiBundle\Annotation\Entity'
            );

            return !is_null($annotation);
        }

        return false;
    }

    protected function extractExposedProperties(\ReflectionClass $reflection)
    {
        $properties = $reflection->getProperties();

        $exposed =  array_filter($properties, function( \ReflectionProperty $property) {
            $annotation = $this->annotationReader->getPropertyAnnotation(
                $property,
                'Ilios\ApiBundle\Annotation\Expose'
            );

            return !is_null($annotation);
        });

        $exposedProperties = [];
        foreach ($exposed as $property) {
            $exposedProperties[$property->name] = $property;
        }

        return $exposedProperties;
    }

    protected function getTypeOfProperty(\ReflectionProperty $property)
    {
        /** @var Type $typeAnnotation */
        $typeAnnotation = $this->annotationReader->getPropertyAnnotation(
            $property,
            'Ilios\ApiBundle\Annotation\Type'
        );

        if (is_null($typeAnnotation)) {
            throw new \Exception(
                "Missing Type annotation on {$property->class}::{$property->getName()}"
            );
        }

        return $typeAnnotation->value;
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
        if (null == $value) {
            return null;
        }

        $type = $this->getTypeOfProperty($property);
        if ($type === 'dateTime') {
            $value = new \DateTime($value);
        }

        if (in_array($type, ['entity', 'entityCollection'])) {
            $manager = $this->registry->getManagerForClass($property->class);
            $metaData = $manager->getClassMetadata($property->class);
            $associationMappings = $metaData->associationMappings;

            $association = $associationMappings[$property->name];
            $targetEntity = $association['targetEntity'];
            $repository = $this->registry->getRepository($targetEntity);

            if ($type === 'entity') {
                $value = $repository->find($value);
            } else {
                if (is_array($value) && !empty($value)) {
                    $value = $repository->findBy(['id' => $value]);
                } else {
                    $value = [];
                }
            }

        }

        return $value;
    }
}