<?php

declare(strict_types=1);

namespace App\Service\GraphQL;

use App\Attribute\Related;
use App\Service\EntityMetadata;
use Exception;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Contracts\Cache\CacheInterface;
use function array_key_exists;
use function array_keys;
use function call_user_func;

class TypeRegistry
{
    private const CACHE_KEY_PREFIX = 'ilios-graphql-type-registry';
    protected array $typeToDtoClassNames;
    protected array $typeRefs = [];
    protected array $types = [];

    public function __construct(protected EntityMetadata $entityMetadata, protected CacheInterface $appCache)
    {
        $this->typeToDtoClassNames = $this->getDtoTypes();
//        $this->typeToDtoRefs = $this->appCache->get(
//            self::CACHE_KEY_PREFIX . 'dto-types',
//            fn () => $this->getDtoTypes()
//        );
    }

    public function getTypes(): array
    {
        return $this->createTypeRegistry();
//        return $this->appCache->get(
//            self::CACHE_KEY_PREFIX,
//            fn () => $this->createTypeRegistry()
//        );
    }

    protected function getRefForType(string $type): ReflectionClass
    {
        if (!array_key_exists($type, $this->typeRefs)) {
            if (!array_key_exists($type, $this->typeToDtoClassNames)) {
                throw new Exception("Invalid Type. No DTO for ${type}");
            }
            $this->typeRefs[$type] = new ReflectionClass($this->typeToDtoClassNames[$type]);
        }

        return $this->typeRefs[$type];
    }

    protected function createTypeRegistry(): array
    {
        $types = [];
        foreach (array_keys($this->typeToDtoClassNames) as $name) {
            $types[$name] = $this->getType($name);
        }
        return $types;
    }

    protected function getDtoTypes(): array
    {
        $dtos = $this->entityMetadata->getDtoList();
        $types = array_map(function (string $className) {
            $ref = new ReflectionClass($className);
            $type = $this->entityMetadata->extractType($ref);
            return [
                'name' => $className,
                'type' => $type
            ];
        }, $dtos);
        $rhett = [];
        foreach ($types as $arr) {
            $rhett[$arr['type']] = $arr['name'];
        }

        return $rhett;
    }

    protected function getType(string $name): ObjectType
    {
        if (!array_key_exists($name, $this->types)) {
            $ref = $this->getRefForType($name);
            $exposedProperties = $this->entityMetadata->extractExposedProperties($ref);
            $fields = [];
            foreach ($exposedProperties as $prop) {
                $fields[$prop->getName()] = $this->buildPropertyField($prop);
            }
            $this->types[$name] =  new ObjectType([
                'name' => $this->entityMetadata->extractType($ref),
                'fields' => $fields,
            ]);
        }
        return $this->types[$name];
    }

    protected function buildPropertyField(ReflectionProperty $property): array
    {
        if ($this->isRelated($property)) {
            $name = $this->entityMetadata->extractRelatedNameForProperty($property);
            return [
                'type' => fn() => $this->getType($name)
            ];
        } else {
            $type = $this->entityMetadata->getTypeOfProperty($property);
            return [
                'type' => match ($type) {
                    'string' => Type::string(),
                    'boolean' => Type::boolean(),
                    'integer' => Type::int(),
                    'float' => Type::float(),
                    'dateTime' => DateTimeType::getInstance(),
                    'array<dto>', 'array<string>', 'array' => Type::listOf(Type::string()),
                }
            ];
        }
    }

    protected function isRelated(ReflectionProperty $property): bool
    {
        $related = $property->getAttributes(Related::class);
        return $related !== [];
    }
}