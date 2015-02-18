<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\SessionInterface;

/**
 * Interface SessionManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface SessionManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return SessionInterface
     */
    public function findSessionBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return SessionInterface[]|Collection
     */
    public function findSessionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param SessionInterface $session
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateSession(
        SessionInterface $session,
        $andFlush = true
    );

    /**
     * @param SessionInterface $session
     *
     * @return void
     */
    public function deleteSession(
        SessionInterface $session
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return SessionInterface
     */
    public function createSession();
}
