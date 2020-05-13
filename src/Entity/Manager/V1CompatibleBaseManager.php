<?php

declare(strict_types=1);

namespace App\Entity\Manager;


use Exception;

/**
 * Class V1CompatibleBaseManager
 */
class V1CompatibleBaseManager extends BaseManager
{
    /**
     * @param array $criteria
     * @return mixed
     */
    public function findV1DTOBy(array $criteria)
    {
        $results = $this->findV1DTOsBy($criteria, null, 1);
        return empty($results) ? false : $results[0];
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return mixed
     * @throws Exception
     */
    public function findV1DTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()->findV1DTOsBy($criteria, $orderBy, $limit, $offset);
    }
}
