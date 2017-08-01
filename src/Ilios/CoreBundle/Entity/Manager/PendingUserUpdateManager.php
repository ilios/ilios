<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Repository\PendingUserUpdateRepository;

/**
 * Class PendingUserUpdateManager
 */
class PendingUserUpdateManager extends BaseManager
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
