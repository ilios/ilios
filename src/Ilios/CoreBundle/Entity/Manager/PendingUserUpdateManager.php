<?php

namespace Ilios\CoreBundle\Entity\Manager;

/**
 * Class PendingUserUpdateManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class PendingUserUpdateManager extends BaseManager
{
    /**
     * Clear all the pending updates.
     */
    public function removeAllPendingUserUpdates()
    {
        return $this->getRepository()->removeAllPendingUserUpdates();
    }
}
