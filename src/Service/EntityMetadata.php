<?php

declare(strict_types=1);

namespace App\Service;

use App\Attributes\DTO;
use App\Attributes\Entity;
use App\Attributes\Expose;
use App\Attributes\FilterableBy;
use App\Attributes\Id;
use App\Attributes\OnlyReadable;
use App\Attributes\Related;
use App\Attributes\RemoveMarkup;
use App\Attributes\Type;
use Doctrine\Persistence\Proxy;
use Exception;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\KernelInterface;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Contracts\Cache\CacheInterface;

use function array_key_exists;

class EntityMetadata
{
    private const string CACHE_KEY_PREFIX = 'ilios-entity-metadata-';
    private array $exposedPropertiesForClass;
    private array $typeForClasses;
    private array $typeForProperties;
    private array $idForClasses;
    private array $relatedForClass;
    private array $iliosEntities;
    private array $iliosDtos;
    private array $entityTypes;
    private array $filtersForClass;

    /**
     * EntityMetadata constructor
     *
     * Build and cache all of the entity and dto class names so
     * we don't have to constantly run expensive class_exists
     * and annotation inspection tasks
     */
    public function __construct(
        CacheInterface $appCache,
        KernelInterface $kernel,
    ) {
        $this->exposedPropertiesForClass = [];
        $this->typeForClasses = [];
        $this->typeForProperties = [];
        $this->idForClasses = [];
        $this->relatedForClass = [];
        $this->filtersForClass = [];

        $this->iliosEntities = $appCache->get(
            self::CACHE_KEY_PREFIX . 'entities',
            fn () => $this->findIliosEntities($kernel)
        );

        $this->iliosDtos = $appCache->get(
            self::CACHE_KEY_PREFIX . 'dtos',
            fn () => $this->findIliosDtos($kernel)
        );

        $this->entityTypes = $appCache->get(
            self::CACHE_KEY_PREFIX . 'entity-types',
            fn () => $this->getEntitiesForDtoTypes()
        );
    }

    /**
     * Check if an object or className has the Entity annotation
     *
     * @param string|object $classNameOrObject
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
     *
     * @param string|object $classNameOrObject
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
            $exposed =  array_filter(
                $reflection->getProperties(),
                fn (ReflectionProperty $property) => $property->getAttributes(Expose::class) !== []
            );

            $exposedProperties = [];
            foreach ($exposed as $property) {
                $exposedProperties[$property->name] = $property;
            }

            $this->exposedPropertiesForClass[$className] = $exposedProperties;
        }

        return $this->exposedPropertiesForClass[$className];
    }

    /**
     * Get filterable options for a DTO
     */
    public function extractFilterable(ReflectionClass $reflection): array
    {
        $className = $reflection->getName();
        if (!array_key_exists($className, $this->filtersForClass)) {
            $filters = [];
            foreach ($reflection->getAttributes(FilterableBy::class) as $attribute) {
                $arr = $attribute->getArguments();
                $filters[$arr[0]] = $arr[1];
            }

            $this->filtersForClass[$className] = $filters;
        }

        return $this->filtersForClass[$className];
    }

    /**
     * Get the ID property for a class
     */
    public function extractId(ReflectionClass $reflection): string
    {
        $className = $reflection->getName();
        if (!array_key_exists($className, $this->idForClasses)) {
            $ids = array_filter(
                $reflection->getProperties(),
                fn (ReflectionProperty $property) => $property->getAttributes(Id::class) !== []
            );
            if (!$ids) {
                throw new Exception("{$className} has no property annotated with @Id");
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
                $related = $property->getAttributes(Related::class);
                $exposed = $property->getAttributes(Expose::class);
                if ($related !== [] && $exposed !== []) {
                    $relatedProperties[$property->getName()] = $this->extractRelatedNameForProperty($property);
                }
            }

            $this->relatedForClass[$className] = $relatedProperties;
        }

        return $this->relatedForClass[$className];
    }

    /**
     * Extract the name of the related data from the annotation
     * defaults to the name of the property if not set
     */
    public function extractRelatedNameForProperty(ReflectionProperty $property): string
    {
        $related = $property->getAttributes(Related::class);
        if ($related === []) {
            throw new Exception("No related annotation on " . $property->getName());
        }
        $arguments = $related[0]->getArguments();
        return $arguments[0] ?? $property->getName();
    }

    /**
     * Get the JSON:API type of an object
     */
    public function extractType(ReflectionClass $reflection): string
    {
        $className = $reflection->getName();
        if (!array_key_exists($className, $this->typeForClasses)) {
            $attributes = $reflection->getAttributes(DTO::class);
            if ($attributes === []) {
                throw new Exception(
                    "Missing Type attribute {$className} DTO Attribute"
                );
            }
            $arguments = $attributes[0]->getArguments();
            if ($arguments === []) {
                throw new Exception(
                    "Missing Type argument on {$className} DTO Attribute"
                );
            }
            $type = $arguments[0];

            $this->typeForClasses[$className] = $type;
        }

        return $this->typeForClasses[$className];
    }

    /**
     * Get all of the properties of a class which are
     * not annotated as OnlyReadable
     */
    public function extractWritableProperties(ReflectionClass $reflection): array
    {
        $exposedProperties = $this->extractExposedProperties($reflection);

        return array_filter(
            $exposedProperties,
            fn(ReflectionProperty $property) => !$this->isPropertyOnlyReadable($property)
        );
    }

    /**
     * Get all of the annotated OnlyReadable properties of a class
     */
    public function extractOnlyReadableProperties(ReflectionClass $reflection): array
    {
        $exposedProperties = $this->extractExposedProperties($reflection);

        return array_filter(
            $exposedProperties,
            fn(ReflectionProperty $property) => $this->isPropertyOnlyReadable($property)
        );
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
            $attributes = $property->getAttributes(Type::class);
            if ($attributes === []) {
                throw new Exception(
                    "Missing Type attribute on {$className}::{$propertyName}"
                );
            }
            $arguments = $attributes[0]->getArguments();
            if ($arguments === []) {
                throw new Exception(
                    "Missing Type argument on {$className}::{$propertyName}"
                );
            }
            $this->typeForProperties[$key] = $arguments[0];
        }

        return $this->typeForProperties[$key];
    }

    /**
     * Check if a property has the OnlyReadable annotation
     */
    public function isPropertyOnlyReadable(ReflectionProperty $property): bool
    {
        return $property->getAttributes(OnlyReadable::class) !== [];
    }

    /**
     * Check if a property has the RemoveMarkup annotation
     */
    public function isPropertyRemoveMarkup(ReflectionProperty $property): bool
    {
        return $property->getAttributes(RemoveMarkup::class) !== [];
    }

    /**
     * Get the list of DTOs
     */
    public function getDtoList(): array
    {
        return $this->iliosDtos;
    }

    /**
     * Get the list of Entities
     */
    public function getEntityList(): array
    {
        return $this->iliosEntities;
    }

    /**
     * Get the entity name for a type
     */
    public function getEntityForType(string $type): string
    {
        if (!array_key_exists($type, $this->entityTypes)) {
            throw new Exception("Invalid Type. No DTO for {$type}");
        }

        return $this->entityTypes[$type];
    }

    /**
     * Scan filesystem for classes matching an attribute
     */
    protected function findByAttribute(Finder $files, string $namespace, string $attribute): array
    {
        $classNames = array_map(
            fn(SplFileInfo $file) => $namespace . '\\' . $file->getBasename('.php'),
            array_values(iterator_to_array($files))
        );

        return array_filter($classNames, function (string $name) use ($attribute) {
            $reflection = new ReflectionClass($name);
            $attributes = $reflection->getAttributes($attribute);
            return $attributes !== [];
        });
    }

    /**
     * Load Entities by scanning the file system
     * then use that list to discover those which have the
     * correct annotation
     */
    protected function findIliosEntities(KernelInterface $kernel): array
    {
        $path = $kernel->getProjectDir() . '/src/Entity';
        $finder = new Finder();
        $files = $finder->in($path)->files()->depth("== 0")->notName('*Interface.php')->sortByName();
        return $this->findByAttribute($files, 'App\\Entity', Entity::class);
    }

    /**
     * Load classes by scanning directories then use
     * that list to discover classes which have the DTO annotation
     */
    protected function findIliosDtos(KernelInterface $kernel): array
    {
        $dtoPath = $kernel->getProjectDir() . '/src/Entity/DTO';
        $finder = new Finder();
        $files = $finder->in($dtoPath)->files()->depth("== 0")->sortByName();
        $dtos = $this->findByAttribute($files, 'App\\Entity\\DTO', DTO::class);

        $classPath = $kernel->getProjectDir() . '/src/Classes';
        $finder = new Finder();
        $files = $finder->in($classPath)->files()->depth("== 0")->sortByName();
        $classes = $this->findByAttribute($files, 'App\\Classes', DTO::class);

        return [...$dtos, ...$classes];
    }

    /**
     * Get an Entity class name for each DTO type
     */
    protected function getEntitiesForDtoTypes(): array
    {
        $rhett = [];
        foreach ($this->iliosDtos as $className) {
            $ref = new ReflectionClass($className);
            $type = $this->extractType($ref);

            //drop DTO suffix from the name
            $name = substr($className, 15, -3);
            $class = "App\\Entity\\{$name}";
            if (class_exists($class, false)) {
                $rhett[$type] = $class;
            }
        }

        return $rhett;
    }
}
