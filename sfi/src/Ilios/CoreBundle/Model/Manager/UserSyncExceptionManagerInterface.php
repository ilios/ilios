<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\UserSyncExceptionInterface;

/**
 * Interface UserSyncExceptionManagerInterface
 */
interface UserSyncExceptionManagerInterface
{
    /** 
     *@return UserSyncExceptionInterface
     */
    public function createUserSyncException();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return UserSyncExceptionInterface
     */
    public function findUserSyncExceptionBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return UserSyncExceptionInterface[]|Collection
     */
    public function findUserSyncExceptionsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param UserSyncExceptionInterface $userSyncException
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateUserSyncException(UserSyncExceptionInterface $userSyncException, $andFlush = true);

    /**
     * @param UserSyncExceptionInterface $userSyncException
     *
     * @return void
     */
    public function deleteUserSyncException(UserSyncExceptionInterface $userSyncException);

    /**
     * @return string
     */
    public function getClass();
}
