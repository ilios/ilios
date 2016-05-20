<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\PendingUserUpdateInterface;

/**
 * Class PendingUserUpdateManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class PendingUserUpdateManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findPendingUserUpdateBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findPendingUserUpdatesBy(
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
    public function updatePendingUserUpdate(
        PendingUserUpdateInterface $pendingUserUpdate,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($pendingUserUpdate, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deletePendingUserUpdate(
        PendingUserUpdateInterface $pendingUserUpdate
    ) {
        $this->delete($pendingUserUpdate);
    }

    /**
     * @deprecated
     */
    public function createPendingUserUpdate()
    {
        return $this->create();
    }

    /**
     * Clear all the pending updates.
     */
    public function removeAllPendingUserUpdates()
    {
        return $this->getRepository()->removeAllPendingUserUpdates();
    }
}
