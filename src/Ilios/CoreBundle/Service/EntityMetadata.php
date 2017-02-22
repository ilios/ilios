<?php

namespace Ilios\CoreBundle\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Cache\FilesystemCache;
use Ilios\ApiBundle\Annotation\ReadOnly;
use Ilios\ApiBundle\Annotation\Type;

class EntityMetadata
{
    const CACHE_FILE_NAME = '/ilios/annotations';

    /**
     * @var Reader
     */
    private $annotationReader;

    public function __construct($environment, $kernelCacheDir)
    {
        if ($environment === 'prod') {
            $cache = new ApcuCache();
        } else {
            $cache = new FilesystemCache($kernelCacheDir . self::CACHE_FILE_NAME);
        }

        $this->annotationReader = new CachedReader(
            new AnnotationReader(),
            $cache,
            $debug = $environment !== 'prod'
        );
    }

    public function isAnIliosEntity($classNameOrObject)
    {
        if ((is_string($classNameOrObject) && class_exists($classNameOrObject)) ||
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

    public function isAnIliosDto($classNameOrObject)
    {
        if ((is_string($classNameOrObject) && class_exists($classNameOrObject)) ||
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

    public function extractExposedProperties(\ReflectionClass $reflection)
    {
        $properties = $reflection->getProperties();

        $exposed =  array_filter($properties, function (\ReflectionProperty $property) {
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

    public function extractWritableProperties(\ReflectionClass $reflection)
    {
        $exposedProperties = $this->extractExposedProperties($reflection);

        return array_filter($exposedProperties, function (\ReflectionProperty $property) {
            return !$this->isPropertyReadOnly($property);
        });
    }

    public function extractReadOnlyProperties(\ReflectionClass $reflection)
    {
        $exposedProperties = $this->extractExposedProperties($reflection);

        return array_filter($exposedProperties, function (\ReflectionProperty $property) {
            return $this->isPropertyReadOnly($property);
        });
    }

    public function getTypeOfProperty(\ReflectionProperty $property)
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

    public function isPropertyReadOnly(\ReflectionProperty $property)
    {
        /** @var ReadOnly $annotation */
        $annotation = $this->annotationReader->getPropertyAnnotation(
            $property,
            'Ilios\ApiBundle\Annotation\ReadOnly'
        );

        return !is_null($annotation);
    }
}
