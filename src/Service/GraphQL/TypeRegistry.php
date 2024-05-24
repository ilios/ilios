<?php

declare(strict_types=1);

namespace App\Service\GraphQL;

use App\Attributes as IA;
use App\Service\EntityMetadata;
use Doctrine\Inflector\Inflector;
use Exception;
use GraphQL\Type\Definition\IDType;
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
        protected Inflector $inflector,
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
            ],
        ];

        if ($includeArgs) {
            $def['args'] = $this->buildArgs($name);
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
            $fn = in_array($type, [IA\Type::STRINGS, IA\Type::INTEGERS]) ?
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
                    IA\Type::DTOS, IA\Type::STRINGS, 'array' => Type::listOf(Type::string()),
                    default => throw new Exception("Unhandled property type $type encountered."),
                },
                'resolve' => $this->fieldResolver,
            ];
        }
    }

    protected function buildArgs(string $name): array
    {
        $filters = [];
        $exposedProperties = $this->dtoInfo->getGraphQLExposedPropertiesForType($name);
        $idProperties = array_filter($exposedProperties, [$this, 'isId']);

        $idProperty = array_values($idProperties)[0];
        $type = $this->entityMetadata->getTypeOfProperty($idProperty);
        $propertyName = $idProperty->getName();
        $filters[$propertyName] = ['type' => match ($type) {
            'string' => IDType::string(),
            'integer' => IDType::int(),
            default => throw new Exception("Unhandled property type $type encountered."),
        } ];
        $filters[$this->inflector->pluralize($propertyName)] = ['type' => Type::listOf(match ($type) {
            'string' => IDType::string(),
            'integer' => IDType::int(),
            default => throw new Exception("Unhandled property type $type encountered."),
        }) ];

        $notIdProperties = array_diff($exposedProperties, $idProperties);
        foreach ($notIdProperties as $property) {
            $type = $this->entityMetadata->getTypeOfProperty($property);
            $propertyName = $property->getName();
            $filters[$propertyName] = ['type' => $this->getFilterType($type)];
        }

        foreach ($this->entityMetadata->extractFilterable($this->dtoInfo->getRefForType($name)) as $name => $type) {
            $filters[$name] = $this->getFilterType($type);
        }

        return $filters;
    }

    protected function getFilterType(string $type): Type
    {
        return match ($type) {
            'string' => Type::string(),
            IA\Type::DTOS, IA\Type::STRINGS => Type::listOf(Type::string()),
            'boolean' => Type::boolean(),
            'integer', 'entity' => Type::int(),
            IA\Type::INTEGERS => Type::listOf(Type::int()),
            'float' => Type::float(),
            'dateTime' => DateTimeType::getInstance(),
            default => throw new Exception("Unhandled property type $type encountered."),
        };
    }

    protected function isId(ReflectionProperty $property): bool
    {
        $id = $property->getAttributes(IA\Id::class);
        return $id !== [];
    }
}
