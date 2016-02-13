<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Classes\UserEvent;
use Ilios\CoreBundle\Classes\CalendarEvent;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Entity\DTO\UserDTO;

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
     *
     * @return UserDTO|bool a user object or FALSE if none were found.
     */
    public function findUserDTOBy(
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
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return UserDTO[]
     */
    public function findUserDTOsBy(
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
     * @param array $criteria
     *
     * @return UserInterface[]
     */
    public function findUsersByQ(
        $q,
        array $orderBy = null,
        $limit = null,
        $offset = null,
        array $criteria = array()
    );
    
    /**
     * @param array $campusIdFilter an array of the campusIDs to include in our search if empty then all users
     *
     * @return ArrayCollection
     */
    public function findUsersWhoAreNotFormerStudents(array $campusIdFilter = array());
    
    /**
     * Get all the campus IDs for every user
     * @param $includeDisabled
     * @param $includeSyncIgnore
     *
     * @return array
     */
    public function getAllCampusIds($includeDisabled = true, $includeSyncIgnore = true);
    
    /**
     * Reset the examined flags on every user
     */
    public function resetExaminedFlagForAllUsers();

    /**
     * Find all of the events for a user id between two dates.
     *
     * @param integer $userId
     * @param \DateTime $from
     * @param \DateTime $to
     * @return UserEvent[]
     */
    public function findEventsForUser($userId, \DateTime $from, \DateTime $to);

    /**
     * Finds and adds instructors to a given list of calendar events.
     *
     * @param CalendarEvent[] $events
     * @return CalendarEvent[]
     */
    public function addInstructorsToEvents(array $events);
}
