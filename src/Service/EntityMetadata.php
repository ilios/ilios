<?php

declare(strict_types=1);

namespace App\Service;

use App\Annotation\DTO;
use App\Annotation\Expose;
use App\Annotation\Id;
use App\Annotation\Related;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Cache\Cache;
use Doctrine\Persistence\Proxy;
use App\Annotation\ReadOnly;
use App\Annotation\Type;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\KernelInterface;
use ReflectionClass;
use ReflectionProperty;
use is_null;

class EntityMetadata
{
    private const CACHE_KEY_PREFIX = 'ilios-entity-metadata-';
    private Reader $annotationReader;
    private array $exposedPropertiesForClass;
    private array $typeForClasses;
    private array $typeForProperties;
    private array $idForClasses;
    private array $relatedForClass;
    private array $iliosEntities;
    private array $iliosDtos;

    /**
     * EntityMetadata constructor
     *
     * Build and cache all of the entity and dto class names so
     * we don't have to constantly run expensive class_exists
     * and annotation inspection tasks
     */
    public function __construct(Cache $cache, KernelInterface $kernel)
    {
        $this->exposedPropertiesForClass = [];
        $this->typeForClasses = [];
        $this->typeForProperties = [];
        $this->idForClasses = [];
        $this->relatedForClass = [];

        $this->annotationReader = new CachedReader(
            new AnnotationReader(),
            $cache,
            $kernel->getEnvironment() !== 'prod'
        );

        $entityKey = self::CACHE_KEY_PREFIX . 'entities';
        if (!$cache->contains($entityKey) || !$entities = $cache->fetch($entityKey)) {
            $entities = $this->findIliosEntities($kernel);
            $cache->save($entityKey, $entities);
        }

        $this->iliosEntities = $entities;

        $dtoKey = self::CACHE_KEY_PREFIX . 'dtos';
        if (!$cache->contains($dtoKey) || !$dtos = $cache->fetch($dtoKey)) {
            $dtos = $this->findIliosDtos($kernel);
            $cache->save($dtoKey, $dtos);
        }

        $this->iliosDtos = $dtos;
    }

    /**
     * Check if an object or className has the Entity annotation
     */
    public function isAnIliosEntity(mixed $classNameOrObject): bool
    {
        if ($this->isAStringOrClass($classNameOrObject)) {
            $className = $this->getClassName($classNameOrObject);

            if (in_array($className, $this->iliosEntities)) {
                return true;
            }

            if (str_contains($className, 'Proxies')) {
                $reflection = new ReflectionClass($classNameOrObject);
                if ($reflection->implementsInterface(Proxy::class)) {
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
     */
    public function isAnIliosDto(mixed $classNameOrObject): bool
    {
        if ($this->isAStringOrClass($classNameOrObject)) {
            $className = $this->getClassName($classNameOrObject);

            return in_array($className, $this->iliosDtos);
        }

        return false;
    }

    /**
     * Checks to see if what we have been passed is a string or a class
     */
    protected function isAStringOrClass(mixed $classNameOrObject): bool
    {
        return is_string($classNameOrObject) || is_object($classNameOrObject);
    }

    /**
     * Gets the name of a class
     */
    protected function getClassName(string|object $classNameOrObject): string
    {
        return is_object($classNameOrObject) ? $classNameOrObject::class : $classNameOrObject;
    }

    /**
     * Get all of the properties of a call which are
     * marked with the Exposed annotation
     */
    public function extractExposedProperties(ReflectionClass $reflection): array
    {
        $className = $reflection->getName();
        if (!array_key_exists($className, $this->exposedPropertiesForClass)) {
            $properties = $reflection->getProperties();
            $exposed =  array_filter($properties, function (ReflectionProperty $property) {
                $attributes = $property->getAttributes(\App\Attribute\Expose::class);
                if (count($attributes)) {
                    return true;
                } else {
                    $annotation = $this->annotationReader->getPropertyAnnotation(
                        $property,
                        Expose::class
                    );

                    return !is_null($annotation);
                }
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
     * Get the ID property for a class
     */
    public function extractId(ReflectionClass $reflection): string
    {
        $className = $reflection->getName();
        if (!array_key_exists($className, $this->idForClasses)) {
            $properties = $reflection->getProperties();

            $ids = array_filter($properties, function (ReflectionProperty $property) {
                $attributes = $property->getAttributes(\App\Attribute\Id::class);
                if (count($attributes)) {
                    return true;
                }
                $annotation = $this->annotationReader->getPropertyAnnotation(
                    $property,
                    Id::class
                );

                return !is_null($annotation);
            });
            if (!$ids) {
                throw new \Exception("${className} has no property annotated with @Id");
            }

            $this->idForClasses[$className] = array_values($ids)[0]->getName();
        }

        return $this->idForClasses[$className];
    }

    /**
     * Get the related property for a class
     */
    public function extractRelated(ReflectionClass $reflection): array
    {
        $className = $reflection->getName();
        if (!array_key_exists($className, $this->relatedForClass)) {
            $properties = $reflection->getProperties();

            $relatedProperties = [];

            foreach ($properties as $property) {
                $related = $property->getAttributes(\App\Attribute\Related::class);
                $exposed = $property->getAttributes(\App\Attribute\Expose::class);
                if (count($related)) {
                    if (count($exposed)) {
                        $arguments = $related[0]->getArguments();
                        $value = count($arguments) ? $arguments[0] : $property->getName();
                        $relatedProperties[$property->getName()] = $value;
                    }
                } else {
                    $related = $this->annotationReader->getPropertyAnnotation(
                        $property,
                        Related::class
                    );
                    $exposed = $this->annotationReader->getPropertyAnnotation(
                        $property,
                        Expose::class
                    );

                    if ($related && $exposed) {
                        $relatedProperties[$property->getName()] =
                            $related->value ? $related->value : $property->getName();
                    }
                }
            }

            $this->relatedForClass[$className] = $relatedProperties;
        }

        return $this->relatedForClass[$className];
    }

    /**
     * Get the JSON:API type of an object
     */
    public function extractType(ReflectionClass $reflection): string
    {
        $className = $reflection->getName();
        if (!array_key_exists($className, $this->typeForClasses)) {
            $attributes = $reflection->getAttributes(\App\Attribute\DTO::class);
            if (count($attributes)) {
                $arguments = $attributes[0]->getArguments();
                if (!count($arguments)) {
                    throw new \Exception(
                        "Missing Type argument on {$className} DTO Attribute"
                    );
                }
                $type = $arguments[0];
            } else {
                $annotation = $this->annotationReader->getClassAnnotation(
                    $reflection,
                    DTO::class
                );
                $type = $annotation->value;
            }

            $this->typeForClasses[$className] = $type;
        }

        return $this->typeForClasses[$className];
    }

    /**
     * Get all of the properties of a class which are
     * not annotated as ReadOnly
     */
    public function extractWritableProperties(ReflectionClass $reflection): array
    {
        $exposedProperties = $this->extractExposedProperties($reflection);

        return array_filter($exposedProperties, function (ReflectionProperty $property) {
            return !$this->isPropertyReadOnly($property);
        });
    }

    /**
     * Get all of the annotated ReadOnly properties of a class
     */
    public function extractReadOnlyProperties(ReflectionClass $reflection): array
    {
        $exposedProperties = $this->extractExposedProperties($reflection);

        return array_filter($exposedProperties, function (ReflectionProperty $property) {
            return $this->isPropertyReadOnly($property);
        });
    }

    /**
     * Get the Type annotation of a property
     */
    public function getTypeOfProperty(ReflectionProperty $property): string
    {
        $className = $property->class;
        $propertyName = $property->getName();
        $key = $className . $propertyName;

        if (!array_key_exists($key, $this->typeForProperties)) {
            $attributes = $property->getAttributes(\App\Attribute\Type::class);
            if (count($attributes)) {
                $arguments = $attributes[0]->getArguments();
                if (!count($arguments)) {
                    throw new \Exception(
                        "Missing Type argument on {$className}::{$propertyName}"
                    );
                }
                $type = $arguments[0];
            } else {
                /** @var Type $typeAnnotation */
                $typeAnnotation = $this->annotationReader->getPropertyAnnotation(
                    $property,
                    Type::class
                );
                $type = $typeAnnotation?->value;
            }

            if (!isset($type)) {
                throw new \Exception(
                    "Missing Type annotation on {$className}::{$propertyName}"
                );
            }

            $this->typeForProperties[$key] = $type;
        }

        return $this->typeForProperties[$key];
    }

    /**
     * Check if a property has the ReadOnly annotation
     */
    public function isPropertyReadOnly(ReflectionProperty $property): bool
    {
        $attributes = $property->getAttributes(\App\Attribute\ReadOnly::class);
        if (count($attributes)) {
            return true;
        }

        /** @var ReadOnly $annotation */
        $annotation = $this->annotationReader->getPropertyAnnotation(
            $property,
            ReadOnly::class
        );

        return !is_null($annotation);
    }

    /**
     * Check if a property has the RemoveMarkup annotation
     */
    public function isPropertyRemoveMarkup(ReflectionProperty $property): bool
    {
        $attributes = $property->getAttributes(\App\Attribute\RemoveMarkup::class);
        if (count($attributes)) {
            return true;
        }
        /** @var ReadOnly $annotation */
        $annotation = $this->annotationReader->getPropertyAnnotation(
            $property,
            'App\Annotation\RemoveMarkup'
        );

        return !is_null($annotation);
    }

    /**
     * Load Entities by scanning the file system
     * then use that list to discover those which have the
     * correct annotation
     *
     *
     * @return array
     */
    protected function findIliosEntities(KernelInterface $kernel)
    {
        $path = $kernel->getProjectDir() . '/src/Entity';
        $finder = new Finder();
        $files = $finder->in($path)->files()->depth("== 0")->notName('*Interface.php')->sortByName();

        $list = [];
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $class = 'App\\Entity' . '\\' . $file->getBasename('.php');
            $annotation = $this->annotationReader->getClassAnnotation(
                new ReflectionClass($class),
                'App\Annotation\Entity'
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
     *
     * @return array
     */
    protected function findIliosDtos(KernelInterface $kernel)
    {
        $dtoPath = $kernel->getProjectDir() . '/src/Entity/DTO';
        $finder = new Finder();
        $files = $finder->in($dtoPath)->files()->depth("== 0")->sortByName();

        $list = [];
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $class = 'App\\Entity\\DTO' . '\\' . $file->getBasename('.php');
            $refl = new ReflectionClass($class);
            $attributes = $refl->getAttributes(\App\Attribute\DTO::class);
            if (count($attributes)) {
                $list[] = $class;
            } else {
                $annotation = $this->annotationReader->getClassAnnotation(
                    $refl,
                    DTO::class
                );
                if (null !== $annotation) {
                    $list[] = $class;
                }
            }
        }

        $classPath = $kernel->getProjectDir() . '/src/Classes';
        $finder = new Finder();
        $files = $finder->in($classPath)->files()->depth("== 0")->sortByName();

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $class = 'App\\Classes' . '\\' . $file->getBasename('.php');
            $annotation = $this->annotationReader->getClassAnnotation(
                new ReflectionClass($class),
                'App\Annotation\DTO'
            );
            if (null !== $annotation) {
                $list[] = $class;
            }
        }

        return $list;
    }
}
