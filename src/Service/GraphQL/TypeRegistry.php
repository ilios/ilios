<?php

declare(strict_types=1);

namespace App\Service\GraphQL;

use App\Attribute\Id;
use App\Service\EntityMetadata;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use ReflectionProperty;
use Symfony\Contracts\Cache\CacheInterface;

use function array_key_exists;

class TypeRegistry
{
    private const CACHE_KEY = 'ilios-graphql-type-registry';
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
        return $this->appCache->get(
            self::CACHE_KEY,
            fn () => $this->createTypeRegistry()
        );
    }

    protected function createTypeRegistry(): array
    {
        $types = [];
        foreach ($this->dtoInfo->getGraphQLTypeList() as $name) {
            $types[$name] = Type::listOf($this->getType($name));
        }
        return $types;
    }

    protected function getType(string $name): ObjectType
    {
        if (!array_key_exists($name, $this->types)) {
            $exposedProperties = $this->dtoInfo->getGraphQLExposedPropertiesForType($name);
            $fields = [];
            foreach ($exposedProperties as $prop) {
                $fields[$prop->getName()] = $this->buildPropertyField($prop);
            }
            $this->types[$name] =  new ObjectType([
                'name' => $this->entityMetadata->extractType($this->dtoInfo->getRefForType($name)),
                'fields' => $fields,
                'resolve' => $this->typeResolver,
                'extensions' => [
                    'dtoClassName' => $name,
                ]
            ]);
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

    protected function isId(ReflectionProperty $property): bool
    {
        $id = $property->getAttributes(Id::class);
        return $id !== [];
    }
}
