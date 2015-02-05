<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\IngestionExceptionInterface;

/**
 * Interface IngestionExceptionManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface IngestionExceptionManagerInterface
{
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
     * @param integer $limit
     * @param integer $offset
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

    /**
     * @return IngestionExceptionInterface
     */
    public function createIngestionException();
}
