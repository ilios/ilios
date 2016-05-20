<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\SessionTypeInterface;

/**
 * Class SessionTypeManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class SessionTypeManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findSessionTypeBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findSessionTypesBy(
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
    public function updateSessionType(
        SessionTypeInterface $sessionType,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($sessionType, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteSessionType(
        SessionTypeInterface $sessionType
    ) {
        $this->delete($sessionType);
    }

    /**
     * @deprecated
     */
    public function createSessionType()
    {
        return $this->create();
    }
}
