<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\CISessionInterface;

/**
 * Interface CISessionManagerInterface
 */
interface CISessionManagerInterface
{
    /** 
     *@return CISessionInterface
     */
    public function createCISession();

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
     * @param int $limit
     * @param int $offset
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
}
