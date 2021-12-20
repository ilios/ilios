<?php

declare(strict_types=1);

namespace App\Service\GraphQL;

use App\Attribute\Id;
use App\Service\EntityMetadata;
use App\Service\InflectorFactory;
use Doctrine\Inflector\Inflector;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use ReflectionProperty;
use Symfony\Contracts\Cache\CacheInterface;

use function array_key_exists;

class TypeRegistry
{
    protected array $types = [];

    public function __construct(
        protected EntityMetadata $entityMetadata,
        protected CacheInterface $appCache,
        protected DTOInfo $dtoInfo,
        protected TypeResolver $typeResolver,
        protected FieldResolver $fieldResolver,
    ) {
    }

    public function getTypes(): array
    {
        $types = [];
        foreach ($this->dtoInfo->getGraphQLTypeList() as $name) {
            $types[$name] = $this->getTypeDefinition($name, true);
            $types[$name]['type'] = Type::listOf($this->getType($name));
        }
        return $types;
    }

    protected function getTypeDefinition(string $name, bool $includeArgs = false): array
    {
        $exposedProperties = $this->dtoInfo->getGraphQLExposedPropertiesForType($name);
        $fields = [];
        foreach ($exposedProperties as $prop) {
            $fields[$prop->getName()] = $this->buildPropertyField($prop);
        }

        $def = [
            'name' => $this->entityMetadata->extractType($this->dtoInfo->getRefForType($name)),
            'fields' => $fields,
            'resolve' => $this->typeResolver,
            'extensions' => [
                'dtoClassName' => $name,
            ]
        ];

        if ($includeArgs) {
            $def['args'] = $this->buildArgs($exposedProperties);
        }

        return $def;
    }

    protected function getType(string $name): ObjectType
    {
        if (!array_key_exists($name, $this->types)) {
            $def = $this->getTypeDefinition($name);
            $this->types[$name] = new ObjectType($def);
        }

        return $this->types[$name];
    }

    protected function buildPropertyField(ReflectionProperty $property): array
    {
        if ($this->isId($property)) {
            return [
                'type' => Type::id(),
                'resolve' => $this->fieldResolver,
            ];
        } elseif ($this->dtoInfo->isRelated($property)) {
            $type = $this->entityMetadata->getTypeOfProperty($property);
            $name = $this->entityMetadata->extractRelatedNameForProperty($property);

            //wrap array types in listOf
            $fn = $type === 'array<string>' ?
                fn() => Type::listOf($this->getType($name)) :
                fn() => $this->getType($name)
            ;

            return [
                'type' => $fn,
                'resolve' => $this->typeResolver,
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
                },
                'resolve' => $this->fieldResolver,
            ];
        }
    }

    /**
     * @param ReflectionProperty[] $properties
     */
    protected function buildArgs(array $properties): array
    {
        $idProperties = array_filter($properties, [$this, 'isId']);
        if ($idProperties === []) {
            return [];
        }
        $idProperty = array_values($idProperties)[0];
        $type = $this->entityMetadata->getTypeOfProperty($idProperty);
        $name = $idProperty->getName();
        return [
            $name => [
                'type' => Type::listOf(match ($type) {
                    'string' => Type::string(),
                    'integer' => Type::int(),
                })
            ]
        ];
    }

    protected function isId(ReflectionProperty $property): bool
    {
        $id = $property->getAttributes(Id::class);
        return $id !== [];
    }
}
