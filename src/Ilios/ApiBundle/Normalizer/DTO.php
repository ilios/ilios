<?php

namespace Ilios\ApiBundle\Normalizer;

use Ilios\ApiBundle\Annotation\Type;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Ilios DTO normalizer
 */
class DTO extends ObjectNormalizer
{
    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * @param Reader $annotationReader
     */
    public function setReader(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
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
    public function supportsNormalization($classNameOrObject, $format = null)
    {
        if (
            (is_string($classNameOrObject) && class_exists($classNameOrObject)) ||
            is_object($classNameOrObject)
        ) {
            $annotation = $this->annotationReader->getClassAnnotation(
                new \ReflectionClass($classNameOrObject),
                'Ilios\ApiBundle\Annotation\DTO'
            );

            return !is_null($annotation);
        }

        return false;
    }
}