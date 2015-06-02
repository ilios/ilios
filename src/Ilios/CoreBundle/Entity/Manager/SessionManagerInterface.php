<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\SessionInterface;

/**
 * Interface SessionManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
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
     * @return ArrayCollection|SessionInterface[]
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
     * @param bool $forceId
     *
     * @return void
     */
    public function updateSession(
        SessionInterface $session,
        $andFlush = true,
        $forceId = false
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
