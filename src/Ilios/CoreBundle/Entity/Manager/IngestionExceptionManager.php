<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\IngestionExceptionInterface;

/**
 * Class IngestionExceptionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class IngestionExceptionManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findIngestionExceptionBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findIngestionExceptionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateIngestionException(
        IngestionExceptionInterface $ingestionException,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($ingestionException, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteIngestionException(
        IngestionExceptionInterface $ingestionException
    ) {
        $this->delete($ingestionException);
    }

    /**
     * @deprecated
     */
    public function createIngestionException()
    {
        return $this->create();
    }
}
