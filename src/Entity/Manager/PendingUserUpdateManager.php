<?php

declare(strict_types=1);

namespace App\Entity\Manager;

use App\Repository\PendingUserUpdateRepository;

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
