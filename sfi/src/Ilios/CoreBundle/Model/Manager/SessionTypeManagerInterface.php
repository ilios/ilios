<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\SessionTypeInterface;

/**
 * Interface SessionTypeManagerInterface
 */
interface SessionTypeManagerInterface
{
    /** 
     *@return SessionTypeInterface
     */
    public function createSessionType();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return SessionTypeInterface
     */
    public function findSessionTypeBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return SessionTypeInterface[]|Collection
     */
    public function findSessionTypesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param SessionTypeInterface $sessionType
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateSessionType(SessionTypeInterface $sessionType, $andFlush = true);

    /**
     * @param SessionTypeInterface $sessionType
     *
     * @return void
     */
    public function deleteSessionType(SessionTypeInterface $sessionType);

    /**
     * @return string
     */
    public function getClass();
}
