<?php

namespace Ilios\CoreBundle\Entity\Manager;

/**
 * This interface defines methods for querying the data store.
 * All query results are returned as Data Transfer Objects (DTO).
 *
 * Interface ManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface DTOManagerInterface
{
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
     * Searches the data store for a single object by given criteria and sort order.
     *
     * @param array $criteria
     * @param array $orderBy
     * @return object|bool The first found object, or FALSE if none could be found.
     */
    public function findDTOBy(array $criteria, array $orderBy = null);
}
