<?php

declare(strict_types=1);

namespace App\Service\GraphQL;

use App\Service\EntityMetadata;
use App\Service\EntityRepositoryLookup;
use Exception;

use function array_diff;
use function array_key_exists;
use function array_keys;

class DeferredBuffer
{
    protected array $buffer = [];
    protected array $cachedValues = [];

    public function __construct(
        protected DTOInfo $dtoInfo,
        protected EntityMetadata $entityMetadata,
        protected EntityRepositoryLookup $entityRepositoryLookup,
    ) {
    }

    public function bufferRequest(string $type, array $ids): void
    {
        if (!array_key_exists($type, $this->buffer)) {
            $this->buffer[$type] = [];
        }
        $this->buffer[$type] = array_merge($this->buffer[$type], $ids);
    }

    public function getValuesForType(string $type, array $ids): array
    {
        $this->processBuffer($type);
        $values = array_map(fn($id) => $this->cachedValues[$type][$id] ?? null, $ids);
        $nonNullValues = array_filter($values);
        if (count($nonNullValues) !== count($ids)) {
            throw new Exception("Not all Ids were fetched!!");
        }
        return $nonNullValues;
    }

    public function getValueForType(string $type, string|int $id): object
    {
        $this->processBuffer($type);
        return $this->cachedValues[$type][$id];
    }

    protected function processBuffer(string $type): void
    {
        $ids = $this->buffer[$type] ?? [];
        if (!array_key_exists($type, $this->cachedValues)) {
            $this->cachedValues[$type] = [];
        }
        $cachedIds = array_keys($this->cachedValues[$type]);
        $uncachedIds = array_diff($ids, $cachedIds);
        if ($uncachedIds !== []) {
            $repository = $this->entityRepositoryLookup->getRepositoryForEndpoint($type);
            $ref = $this->dtoInfo->getRefForType($type);
            $idField = $this->entityMetadata->extractId($ref);
            $values = $repository->findDTOsBy([
                "{$idField}" => $uncachedIds
            ]);
            foreach ($values as $dto) {
                $this->cachedValues[$type][$dto->{$idField}] = $dto;
            }
        }
    }
}
