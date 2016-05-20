<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\IlmSessionInterface;

/**
 * Class IlmSessionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class IlmSessionManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findIlmSessionBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findIlmSessionsBy(
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
    public function updateIlmSession(
        IlmSessionInterface $ilmSession,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($ilmSession, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteIlmSession(
        IlmSessionInterface $ilmSession
    ) {
        $this->delete($ilmSession);
    }

    /**
     * @deprecated
     */
    public function createIlmSession()
    {
        return $this->create();
    }
}
