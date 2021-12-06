<?php

declare(strict_types=1);

namespace App\Repository;

/**
 * Interface RepositoryInterface
 */
interface RepositoryInterface
{
    /**
     * @return string
     */
    public function getClass(): string;

    /**
     * Flush and clear the entity repository when doing bulk updates
     */
    public function flushAndClear();

    /**
     * Flush the entity repository when doing bulk updates
     */
    public function flush();

    /**
     * @return object|null The entity instance or NULL if the entity can not be found.
     */
    public function findOneBy(
        array $criteria
    );

    /**
     * Find a single entity by it's ID
     * @param mixed $id
     */
    public function findOneById($id): ?object;

    /**
     * Searches the data store for a single object by given criteria and sort order.
     */
    public function findDTOBy(array $criteria): ?object;

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return array A list of entities.
     */
    public function findBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * Searches the data store for all objects matching the given criteria.
     * @param int $limit
     * @param int $offset
     *
     * @return object[] A list of DTOs.
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array;

    /**
     * @param object $entity
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function update(
        $entity,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param object $entity
     *
     * @return void
     */
    public function delete(
        $entity
    );

    /**
     * @return object A new entity.
     */
    public function create(): object;

    /**
     * Get the ID field for this type of entity
     * Usualy it is "id", but sometimes it isn't
     */
    public function getIdField(): string;
}
