<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\SessionInterface;

/**
 * Class SessionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class SessionManager extends DTOManager
{
    /**
     * @deprecated
     */
    public function findSessionBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findSessionDTOBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findDTOBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findSessionsBy(
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
    public function findSessionDTOsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findDTOsBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateSession(
        SessionInterface $session,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($session, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteSession(
        SessionInterface $session
    ) {
        $this->delete($session);
    }

    /**
     * @deprecated
     */
    public function createSession()
    {
        return $this->create();
    }
}
