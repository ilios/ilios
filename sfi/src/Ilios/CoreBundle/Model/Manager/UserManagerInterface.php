<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\UserInterface;

interface UserManagerInterface
{
    /**
     * @return UserInterface
     */
    public function createUser();

    /**
     * @param UserInterface $user
     * @return void
     */
    public function deleteUser(UserInterface $user);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @return UserInterface
     */
    public function findUserBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     * @return UserInterface[]
     */
    public function findUsersBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @return string
     */
    public function getClass();

    /**
     * @param UserInterface $user
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateUser(UserInterface $user, $andFlush = true);
}
