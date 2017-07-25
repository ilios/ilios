<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Repository\PendingUserUpdateRepository;

/**
 * Class PendingUserUpdateManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class PendingUserUpdateManager extends DTOManager
{
    /**
     * Clear all the pending updates.
     */
    public function removeAllPendingUserUpdates()
    {
        /** @var PendingUserUpdateRepository $repository */
        $repository = $this->getRepository();
        return $repository->removeAllPendingUserUpdates();
    }
}
