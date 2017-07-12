<?php

namespace Ilios\ApiBundle\Normalizer;

use Ilios\CoreBundle\Service\EntityMetadata;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Ilios DTO normalizer
 */
class DTO extends ObjectNormalizer
{
    /**
     * @var EntityMetadata
     */
    private $entityMetadata;

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
     * Overridden to remove null values
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
     * Use our annotation type system to correctly turn values
     * into their eventual JSON representation
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

        if ($type === 'string') {
            $value = $this->propertyAccessor->getValue($object, $property);
            return null === $value? null:(string) $value;
        }

        if ($type === 'array<string>') {
            $values = $this->propertyAccessor->getValue($object, $property);
            $stringValues = array_map('strval', $values);

            return array_values($stringValues);
        }

        return $this->propertyAccessor->getValue($object, $property);
    }


    /**
     * Check to see if we can normalize the object or class
     * {@inheritdoc}
     */
    public function supportsNormalization($classNameOrObject, $format = null)
    {
        return $this->entityMetadata->isAnIliosDto($classNameOrObject);
    }
}
