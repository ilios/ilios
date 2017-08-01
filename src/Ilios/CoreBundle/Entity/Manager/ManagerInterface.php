<?php

namespace Ilios\CoreBundle\Entity\Manager;

/**
 * Interface ManagerInterface
 */
interface ManagerInterface
{
    /**
     * @return string
     */
    public function getClass();

    /**
     * Flush and clear the entity manager when doing bulk updates
     */
    public function flushAndClear();

    /**
     * Flush the entity manager when doing bulk updates
     */
    public function flush();

    /**
     * @param array $criteria
     *
     * @return object|null The entity instance or NULL if the entity can not be found.
     */
    public function findOneBy(
        array $criteria
    );

    /**
     * Searches the data store for a single object by given criteria and sort order.
     *
     * @param array $criteria
     * @return object|bool The first found object, or FALSE if none could be found.
     */
    public function findDTOBy(array $criteria);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
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
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return object[] A list of DTOs.
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

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
    public function create();
}
