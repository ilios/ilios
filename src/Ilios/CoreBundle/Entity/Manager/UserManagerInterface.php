<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\UserInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface UserManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface UserManagerInterface extends ManagerInterface
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
     * @return UserInterface
     */
    public function createUser();

    /**
     * @param string $q
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return UserInterface[]|Collection
     */
    public function findUsersByQ(
        $q,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );
    
    /**
     * @param array $campusIdFilter an array of the campusIDs to include in our search if empty then all users
     *
     * @return Collection[UserInterface]
     */
    public function findUsersWhoAreNotFormerStudents(array $campusIdFilter = array());
    
    /**
     * Get all the campus IDs for every user
     * @param $includeDisabled
     * @param $includeSyncIgnore
     *
     * @return []
     */
    public function getAllCampusIds($includeDisabled = true, $includeSyncIgnore = true);
    
    /**
     * Reset the examined flags on every user
     */
    public function resetExaminedFlagForAllUsers();
}
