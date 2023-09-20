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
    public function flushAndClear();

    /**
     * Flush the entity repository when doing bulk updates
     */
    public function flush();

    public function findOneBy(
        array $criteria
    );

    /**
     * Find a single entity by its ID
     * @param mixed $id
     */
    public function findOneById($id): ?object;

    /**
     * @param int $limit
     * @param int $offset
     */
    public function findBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );


    /**
     * @param object $entity
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function update(
        $entity,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param object $entity
     */
    public function delete(
        $entity
    );

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
