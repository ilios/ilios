<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Interface UserManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface UserManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return UserInterface
     */
    public function findUserBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|UserInterface[]
     */
    public function findUsersBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param UserInterface $user
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateUser(
        UserInterface $user,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param UserInterface $user
     *
     * @return void
     */
    public function deleteUser(
        UserInterface $user
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return UserInterface
     */
    public function createUser();
}
