<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\DTO\SessionDTO;
use Ilios\CoreBundle\Entity\SessionInterface;

/**
 * Interface SessionManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface SessionManagerInterface extends ManagerInterface
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
     *
     * @return SessionDTO|bool a session object or FALSE if none were found.
     */
    public function findSessionDTOBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return SessionInterface[]
     */
    public function findSessionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return SessionDTO[]
     */
    public function findSessionDTOsBy(
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
     * @return SessionInterface
     */
    public function createSession();
}
