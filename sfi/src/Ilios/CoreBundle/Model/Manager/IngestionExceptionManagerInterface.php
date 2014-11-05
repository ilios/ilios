<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\IngestionExceptionInterface;

/**
 * Interface IngestionExceptionManagerInterface
 */
interface IngestionExceptionManagerInterface
{
    /** 
     *@return IngestionExceptionInterface
     */
    public function createIngestionException();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return IngestionExceptionInterface
     */
    public function findIngestionExceptionBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return IngestionExceptionInterface[]|Collection
     */
    public function findIngestionExceptionsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param IngestionExceptionInterface $ingestionException
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateIngestionException(IngestionExceptionInterface $ingestionException, $andFlush = true);

    /**
     * @param IngestionExceptionInterface $ingestionException
     *
     * @return void
     */
    public function deleteIngestionException(IngestionExceptionInterface $ingestionException);

    /**
     * @return string
     */
    public function getClass();
}
