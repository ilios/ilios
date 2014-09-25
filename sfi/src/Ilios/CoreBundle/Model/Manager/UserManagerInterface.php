<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\UserInterface;

/**
 * Interface UserManagerInterface
 */
interface UserManagerInterface
{
    /** 
     *@return UserInterface
     */
    public function createUser();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return UserInterface
     */
    public function findUserBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return UserInterface[]|Collection
     */
    public function findUsersBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param UserInterface $user
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateUser(UserInterface $user, $andFlush = true);

    /**
     * @param UserInterface $user
     *
     * @return void
     */
    public function deleteUser(UserInterface $user);

    /**
     * @return string
     */
    public function getClass();
}
