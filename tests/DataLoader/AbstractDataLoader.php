<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Service\EntityMetadata;
use Faker\Factory as FakerFactory;
use ReflectionClass;

/**
 * Abstract utilities for loading data
 *
 *
 */
abstract class AbstractDataLoader implements DataLoaderInterface
{
    protected $data;

    protected $faker;

    /**
     * @var EntityMetadata
     */
    protected $entityMetadata;

    public function __construct(EntityMetadata $entityMetadata)
    {
        $this->faker = FakerFactory::create();
        $this->faker->seed(1234);
        $this->entityMetadata = $entityMetadata;
    }

    /**
     * Create test data
     * @return array
     */
    abstract protected function getData();

    /**
     * [setup description]
     * @return [type] [description]
     */
    protected function setup()
    {
        if (!empty($this->data)) {
            return;
        }

        $this->data = $this->getData();
    }


    public function getOne()
    {
        $this->setUp();
        return array_values($this->data)[0];
    }

    public function getAll()
    {
        $this->setUp();
        return $this->data;
    }

    /**
     * Get a formatted data from a string
     * @param string $when
     * @return string
     */
    public function getFormattedDate($when)
    {
        $dt = new \DateTime($when);
        return $dt->format('c');
    }

    abstract public function create();

    abstract public function createInvalid();

    public function createJsonApi(array $arr): object
    {
        throw new \Exception('Not implemented');
    }

    /**
     * @inheritdoc
     */
    public function createMany($count)
    {
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $arr = $this->create();
            $arr['id'] = $arr['id'] + $i;
            $data[] = $arr;
        }

        return $data;
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
                    foreach ($value as $relId) {
                        $relationships[$attributeName]['data'][] = [
                            'type' => $relationshipType,
                            'id' => $relId,
                        ];
                    }
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
            'relationships' => $relationships
        ];
    }
}
