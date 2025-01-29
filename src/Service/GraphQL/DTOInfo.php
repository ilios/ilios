<?php

declare(strict_types=1);

namespace App\Service\GraphQL;

use App\Attributes\ExposeGraphQL;
use App\Attributes\Related;
use App\Service\EntityMetadata;
use Exception;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Contracts\Cache\CacheInterface;

use function array_filter;
use function array_key_exists;
use function array_keys;

class DTOInfo
{
    private const string CACHE_KEY_PREFIX = 'ilios-dto-info';
    protected array $types;

    public function __construct(
        protected EntityMetadata $entityMetadata,
        protected CacheInterface $appCache,
    ) {
        $this->types = $this->appCache->get(
            self::CACHE_KEY_PREFIX . 'types',
            fn () => $this->getDtoTypes()
        );
    }

    public function getDtoTypeList(): array
    {
        return array_keys($this->types);
    }

    public function getGraphQLTypeList(): array
    {
        $graphQlTypes = array_filter($this->types, fn(array $arr) => $arr['isGraphQL']);
        return array_keys($graphQlTypes);
    }

    public function getRefForType(string $type): ReflectionClass
    {
        if (!array_key_exists($type, $this->types)) {
            throw new Exception("Invalid Type. No DTO for {$type}");
        }
        return new ReflectionClass($this->types[$type]['name']);
    }

    public function isGraphQL(ReflectionClass $class): bool
    {
        $graphQL = $class->getAttributes(ExposeGraphQL::class);
        return $graphQL !== [];
    }

    public function getGraphQLExposedPropertiesForType(string $type): array
    {
        $ref = $this->getRefForType($type);
        $exposedProperties = $this->entityMetadata->extractExposedProperties($ref);
        $relatedProperties = array_filter($exposedProperties, $this->isRelated(...));
        $regularProperties = array_diff($exposedProperties, $relatedProperties);

        $graphQlRelated = array_filter($relatedProperties, [$this, 'isGraphQlRelated']);

        return array_merge($regularProperties, $graphQlRelated);
    }

    public function isRelated(ReflectionProperty $property): bool
    {
        $related = $property->getAttributes(Related::class);
        return $related !== [];
    }

    protected function getDtoTypes(): array
    {
        $dtos = $this->entityMetadata->getDtoList();
        $rhett = [];
        foreach ($dtos as $className) {
            $ref = new ReflectionClass($className);
            $type = $this->entityMetadata->extractType($ref);

            $rhett[$type] = [
                'name' => $className,
                'isGraphQL' => $this->isGraphQL($ref),
            ];
        }

        return $rhett;
    }

    protected function isGraphQlRelated(ReflectionProperty $prop): bool
    {
        $name = $this->entityMetadata->extractRelatedNameForProperty($prop);
        $ref = $this->getRefForType($name);

        return $this->isGraphQL($ref);
    }
}
