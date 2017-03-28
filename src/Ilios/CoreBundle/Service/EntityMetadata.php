<?php

namespace Ilios\CoreBundle\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Cache\Cache;
use Ilios\ApiBundle\Annotation\ReadOnly;
use Ilios\ApiBundle\Annotation\Type;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\KernelInterface;

class EntityMetadata
{
    const CACH_KEY_PREFIX = 'ilios_core-entity-metadata-';

    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * @var array
     */
    private $exposedPropertiesForClass;

    /**
     * @var array
     */
    private $iliosEntities;

    /**
     * @var array
     */
    private $iliosDtos;

    /**
     * EntityMetadata constructor
     *
     * Build and cache all of the entity and dto class names so
     * we don't have to constantly run expensive class_exists
     * and annotation inspection tasks
     *
     * @param Cache $cache
     * @param KernelInterface $kernel
     */
    public function __construct(Cache $cache, KernelInterface $kernel)
    {
        $this->exposedPropertiesForClass = [];

        $this->annotationReader = new CachedReader(
            new AnnotationReader(),
            $cache,
            $debug = $kernel->getEnvironment() !== 'prod'
        );

        $entityKey = self::CACH_KEY_PREFIX . 'entities';
        if (!$cache->contains($entityKey) || !$entities = $cache->fetch($entityKey)) {
            $entities = $this->findIliosEntities($kernel);
            $cache->save($entityKey, $entities);
        }

        $this->iliosEntities = $entities;

        $dtoKey = self::CACH_KEY_PREFIX . 'dtos';
        if (!$cache->contains($dtoKey) || !$dtos = $cache->fetch($dtoKey)) {
            $dtos = $this->findIliosDtos($kernel);
            $cache->save($dtoKey, $dtos);
        }

        $this->iliosDtos = $dtos;
    }

    /**
     * Check if an object or className has the Entity annotation
     *
     * @param $classNameOrObject
     *
     * @return bool
     */
    public function isAnIliosEntity($classNameOrObject)
    {
        if ($this->isAStringOrClass($classNameOrObject)) {
            $className = $this->getClassName($classNameOrObject);

            if (in_array($className, $this->iliosEntities)) {
                return true;
            }

            if (strpos($className, 'Proxies') !== false) {
                $reflection = new \ReflectionClass($classNameOrObject);
                if ($reflection->implementsInterface('Doctrine\Common\Persistence\Proxy')) {
                    $reflection = $reflection->getParentClass();
                    $className = $reflection->getName();

                    return in_array($className, $this->iliosEntities);
                }
            }
        }

        return false;
    }

    /**
     * Check if an object or class name has the DTO annotation
     *
     * @param $classNameOrObject
     *
     * @return bool
     */
    public function isAnIliosDto($classNameOrObject)
    {
        if ($this->isAStringOrClass($classNameOrObject)) {
            $className = $this->getClassName($classNameOrObject);

            return in_array($className, $this->iliosDtos);
        }

        return false;
    }

    /**
     * Checks to see if what we have been passed is a string or a class
     * @param $classNameOrObject
     * @return bool
     */
    protected function isAStringOrClass($classNameOrObject)
    {
        return is_string($classNameOrObject) || is_object($classNameOrObject);
    }

    /**
     * Gets the name of a class
     *
     * @param $classNameOrObject
     * @return string
     */
    protected function getClassName($classNameOrObject)
    {
        return is_object($classNameOrObject)?get_class($classNameOrObject):$classNameOrObject;
    }

    /**
     * Get all of the properties of a call which are
     * marked with the Exposed annotation
     *
     * @param \ReflectionClass $reflection
     *
     * @return mixed
     */
    public function extractExposedProperties(\ReflectionClass $reflection)
    {
        $className = $reflection->getName();
        if (!array_key_exists($className, $this->exposedPropertiesForClass)) {
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

            $this->exposedPropertiesForClass[$className] = $exposedProperties;
        }

        return $this->exposedPropertiesForClass[$className];
    }

    /**
     * Get all of the properties of a class which are
     * not annotated as ReadOnly
     *
     * @param \ReflectionClass $reflection
     *
     * @return array
     */
    public function extractWritableProperties(\ReflectionClass $reflection)
    {
        $exposedProperties = $this->extractExposedProperties($reflection);

        return array_filter($exposedProperties, function (\ReflectionProperty $property) {
            return !$this->isPropertyReadOnly($property);
        });
    }

    /**
     * Get all of the annotated ReadOnly properties of a class
     *
     * @param \ReflectionClass $reflection
     *
     * @return array
     */
    public function extractReadOnlyProperties(\ReflectionClass $reflection)
    {
        $exposedProperties = $this->extractExposedProperties($reflection);

        return array_filter($exposedProperties, function (\ReflectionProperty $property) {
            return $this->isPropertyReadOnly($property);
        });
    }

    /**
     * Get the Type annotation of a property
     *
     * @param \ReflectionProperty $property
     *
     * @return mixed
     *
     * @throws \Exception
     */
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

    /**
     * Check if a property has the ReadOnly annotation
     *
     * @param \ReflectionProperty $property
     *
     * @return bool
     */
    public function isPropertyReadOnly(\ReflectionProperty $property)
    {
        /** @var ReadOnly $annotation */
        $annotation = $this->annotationReader->getPropertyAnnotation(
            $property,
            'Ilios\ApiBundle\Annotation\ReadOnly'
        );

        return !is_null($annotation);
    }

    /**
     * Check if a property has the RemoveMarkup annotation
     *
     * @param \ReflectionProperty $property
     *
     * @return bool
     */
    public function isPropertyRemoveMarkup(\ReflectionProperty $property)
    {
        /** @var ReadOnly $annotation */
        $annotation = $this->annotationReader->getPropertyAnnotation(
            $property,
            'Ilios\ApiBundle\Annotation\RemoveMarkup'
        );

        return !is_null($annotation);
    }

    /**
     * Load Entities by scanning the file system
     * then use that list to discover those which have the
     * correct annotation
     *
     * @param KernelInterface $kernel
     *
     * @return array
     */
    protected function findIliosEntities(KernelInterface $kernel)
    {
        $path = $kernel->locateResource('@IliosCoreBundle/Entity');
        $finder = new Finder();
        $files = $finder->in($path)->files()->depth("== 0")->notName('*Interface.php')->sortByName();

        $list = [];
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $class = 'Ilios\\CoreBundle\\Entity' . '\\' . $file->getBasename('.php');
            $annotation = $this->annotationReader->getClassAnnotation(
                new \ReflectionClass($class),
                'Ilios\ApiBundle\Annotation\Entity'
            );
            if (null !== $annotation) {
                $list[] = $class;
            }
        }

        return $list;
    }

    /**
     * Load classes by scanning directories then use
     * that list to discover classes which have the DTO annotation
     *
     * @param KernelInterface $kernel
     *
     * @return array
     */
    protected function findIliosDtos(KernelInterface $kernel)
    {
        $dtoPath = $kernel->locateResource('@IliosCoreBundle/Entity/DTO');
        $finder = new Finder();
        $files = $finder->in($dtoPath)->files()->depth("== 0")->sortByName();

        $list = [];
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $class = 'Ilios\\CoreBundle\\Entity\\DTO' . '\\' . $file->getBasename('.php');
            $annotation = $this->annotationReader->getClassAnnotation(
                new \ReflectionClass($class),
                'Ilios\ApiBundle\Annotation\DTO'
            );
            if (null !== $annotation) {
                $list[] = $class;
            }
        }

        $classPath = $kernel->locateResource('@IliosCoreBundle/Classes');
        $finder = new Finder();
        $files = $finder->in($classPath)->files()->depth("== 0")->sortByName();

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $class = 'Ilios\\CoreBundle\\Classes' . '\\' . $file->getBasename('.php');
            $annotation = $this->annotationReader->getClassAnnotation(
                new \ReflectionClass($class),
                'Ilios\ApiBundle\Annotation\DTO'
            );
            if (null !== $annotation) {
                $list[] = $class;
            }
        }

        return $list;
    }
}
