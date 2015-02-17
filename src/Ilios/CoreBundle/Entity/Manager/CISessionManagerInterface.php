<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\CISessionInterface;

/**
 * Interface CISessionManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface CISessionManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CISessionInterface
     */
    public function findCISessionBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return CISessionInterface[]|Collection
     */
    public function findCISessionsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param CISessionInterface $cISession
     * @param bool $andFlush
     *
     * @return void
     */
     public function updateCISession(CISessionInterface $cISession, $andFlush = true);

    /**
     * @param CISessionInterface $cISession
     *
     * @return void
     */
    public function deleteCISession(CISessionInterface $cISession);

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return CISessionInterface
     */
    public function createCISession();
}
