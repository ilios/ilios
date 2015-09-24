<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\PendingUserUpdateInterface;

/**
 * Interface PendingUserUpdateManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface PendingUserUpdateManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return PendingUserUpdateInterface
     */
    public function findPendingUserUpdateBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return PendingUserUpdateInterface[]
     */
    public function findPendingUserUpdatesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param PendingUserUpdateInterface $pendingUserUpdate
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updatePendingUserUpdate(
        PendingUserUpdateInterface $pendingUserUpdate,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param PendingUserUpdateInterface $pendingUserUpdate
     *
     * @return void
     */
    public function deletePendingUserUpdate(
        PendingUserUpdateInterface $pendingUserUpdate
    );

    /**
     * @return PendingUserUpdateInterface
     */
    public function createPendingUserUpdate();

    /**
     * Clear all the pending updates
     */
    public function removeAllPendingUserUpdates();
}
