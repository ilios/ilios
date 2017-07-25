<?php

namespace Ilios\CoreBundle\Entity\Manager;

/**
 * This class extends the base entity manger by implementing methods for querying the data store.
 * All query results from these methods are returned as Data Transfer Objects (DTO).
  *
 * Class DTOManager
 */
class DTOManager extends BaseManager implements DTOManagerInterface
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
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()->findDTOsBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Searches the data store for a single object by given criteria and sort order.
     *
     * @param array $criteria
     * @param array $orderBy
     * @return object|bool The first found DTO, or FALSE if none could be found.
     */
    public function findDTOBy(array $criteria, array $orderBy = null)
    {
        $results = $this->getRepository()->findDTOsBy($criteria, $orderBy, 1);
        return empty($results)?false:$results[0];
    }
}
