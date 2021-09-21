<?php

declare(strict_types=1);

namespace App\Service\GraphQL;

use App\Attribute\Id;
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

class DTOInfo
{
    private const CACHE_KEY_PREFIX = 'ilios-graphql-dto-info';
    protected array $typeToDtoClassNames;
    protected array $refs = [];

    public function __construct(
        protected EntityMetadata $entityMetadata,
        protected CacheInterface $appCache,
    ) {
        $this->typeToDtoClassNames = $this->appCache->get(
            self::CACHE_KEY_PREFIX . 'dto-types',
            fn () => $this->getDtoTypes()
        );
    }

    public function getDtoTypeList(): array
    {
        return array_keys($this->typeToDtoClassNames);
    }

    public function getRefForType(string $type): ReflectionClass
    {
        if (!array_key_exists($type, $this->refs)) {
            if (!array_key_exists($type, $this->typeToDtoClassNames)) {
                throw new Exception("Invalid Type. No DTO for ${type}");
            }
            $this->refs[$type] = new ReflectionClass($this->typeToDtoClassNames[$type]);
        }

        return $this->refs[$type];
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
}
