<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Attributes\Id;
use App\Attributes\Related;
use App\Service\EntityRepositoryLookup;
use App\Service\EntityMetadata;
use Closure;
use DateTime;
use ReflectionClass;
use ReflectionProperty;

use function array_filter;
use function array_map;
use function array_values;

/**
 * Abstract utilities for loading data
 */
abstract class AbstractDataLoader implements DataLoaderInterface
{
    protected ?array $data;

    public function __construct(
        protected EntityMetadata $entityMetadata,
        protected EntityRepositoryLookup $entityManagerLookup
    ) {
    }

    /**
     * Create test data
     */
    abstract protected function getData(): array;

    protected function setup(): void
    {
        if (!empty($this->data)) {
            return;
        }

        $this->data = $this->getData();
    }

    public function getOne(): array
    {
        $this->setup();
        return array_values($this->data)[0];
    }

    public function getAll(): array
    {
        $this->setup();
        return $this->data;
    }

    /**
     * Get a formatted data from a string
     */
    public function getFormattedDate(string $when): string
    {
        $dt = new DateTime($when);
        return $dt->format('c');
    }

    abstract public function create(): array;

    abstract public function createInvalid(): array;

    public function createJsonApi(array $arr): object
    {
        $item = $this->buildJsonApiObject($arr, $this->getDtoClass());
        return json_decode(json_encode(['data' => $item]), false);
    }

    public function createMany(int $count): array
    {
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $arr = $this->create();
            $arr['id'] = $arr['id'] + $i;
            $data[] = $arr;
        }

        return $data;
    }

    public function createBulkJsonApi(array $arr): object
    {
        $class = $this->getDtoClass();
        $builder = Closure::fromCallable([$this, 'buildJsonApiObject']);
        $data = array_map(fn(array $item) => $builder($item, $class), $arr);

        return json_decode(json_encode(['data' => $data]), false);
    }

    /**
     * Build a single JSON API version using the annotations on the DTO
     */
    protected function buildJsonApiObject(array $arr, string $dtoClass): array
    {
        $reflection = new ReflectionClass($dtoClass);
        $exposedProperties = $this->entityMetadata->extractExposedProperties($reflection);
        $attributes = [];
        foreach ($exposedProperties as $property) {
            if (array_key_exists($property->name, $arr)) {
                $attributes[$property->name] = $arr[$property->name];
            }
        }

        $relatedProperties = $this->entityMetadata->extractRelated($reflection);
        $type = $this->entityMetadata->extractType($reflection);
        $idProperty = $this->entityMetadata->extractId($reflection);
        $id = $attributes[$idProperty];

        $relationships = [];
        foreach ($relatedProperties as $attributeName => $relationshipType) {
            if (array_key_exists($attributeName, $attributes)) {
                $value = $attributes[$attributeName];
                if (is_array($value)) {
                    $relationships[$attributeName]['data'] = [];
                    foreach ($value as $relId) {
                        $relationships[$attributeName]['data'][] = [
                            'type' => $relationshipType,
                            'id' => $relId,
                        ];
                    }
                } elseif (is_null($value)) {
                    $relationships[$attributeName]['data'] = null;
                } else {
                    $relationships[$attributeName]['data'] = [
                        'type' => $relationshipType,
                        'id' => $value,
                    ];
                }
            }

            unset($attributes[$attributeName]);
        }
        unset($attributes[$idProperty]);

        return [
            'id' => $id,
            'type' => $type,
            'attributes' => $attributes,
            'relationships' => $relationships,
        ];
    }

    /**
     * Get all the scalar fields (not relationships) of a DTO
     */
    public function getScalarFields(): array
    {
        $class = $this->getDtoClass();
        $reflection = new ReflectionClass($class);
        $properties = $this->entityMetadata->extractExposedProperties($reflection);
        $scalarProperties = array_filter(
            $properties,
            fn(ReflectionProperty $p) => !$p->getAttributes(Related::class)
        );

        $names = array_map(fn(ReflectionProperty $p) => $p->name, $scalarProperties);

        return array_values($names);
    }

    /**
     * Get all the scalar fields (not relationships) of a DTO
     */
    public function getIdField(): string
    {
        $class = $this->getDtoClass();
        $reflection = new ReflectionClass($class);
        $properties = $this->entityMetadata->extractExposedProperties($reflection);
        $scalarProperties = array_filter(
            $properties,
            fn(ReflectionProperty $p) => $p->getAttributes(Id::class)
        );

        $names = array_map(fn(ReflectionProperty $p) => $p->name, $scalarProperties);

        return array_values($names)[0];
    }
}
