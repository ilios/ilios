<?php

declare(strict_types=1);

namespace App\Repository;

/**
 * Interface RepositoryInterface
 */
interface RepositoryInterface
{
    public function getClass(): string;

    /**
     * Flush and clear the entity repository when doing bulk updates
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint
    public function flushAndClear();

    /**
     * Flush the entity repository when doing bulk updates
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint
    public function flush();

    // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint
    public function findOneBy(
        array $criteria
    );

    /**
     * Find a single entity by its ID
     */
    public function findOneById(string|int $id): ?object;

    /**
     * Searches the data store for a single object by given criteria and sort order.
     */
    public function findDTOBy(array $criteria): ?object;

    /**
     * @param int $limit
     * @param int $offset
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint,SlevomatCodingStandard.TypeHints.ParameterTypeHint
    public function findBy(
        array $criteria,
        ?array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * Searches the data store for all objects matching the given criteria.
     */
    public function findDTOsBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;

    public function update(
        object $entity,
        bool $andFlush = true,
        bool $forceId = false
    ): void;

    public function delete(
        object $entity
    ): void;

    public function create(): object;

    /**
     * Get the ID field for this type of entity
     * Usually it is "id", but sometimes it isn't
     */
    public function getIdField(): string;

    /**
     * Check if an entity has been persisted to the DB
     * Useful when we don't know if something may have an ID or not
     */
    public function isEntityPersisted(object $entity): bool;
}
