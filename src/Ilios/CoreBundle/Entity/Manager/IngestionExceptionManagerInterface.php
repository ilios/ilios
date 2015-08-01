<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\IngestionExceptionInterface;

/**
 * Interface IngestionExceptionManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface IngestionExceptionManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return IngestionExceptionInterface
     */
    public function findIngestionExceptionBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|IngestionExceptionInterface[]
     */
    public function findIngestionExceptionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param IngestionExceptionInterface $ingestionException
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateIngestionException(
        IngestionExceptionInterface $ingestionException,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param IngestionExceptionInterface $ingestionException
     *
     * @return void
     */
    public function deleteIngestionException(
        IngestionExceptionInterface $ingestionException
    );

    /**
     * @return IngestionExceptionInterface
     */
    public function createIngestionException();
}
