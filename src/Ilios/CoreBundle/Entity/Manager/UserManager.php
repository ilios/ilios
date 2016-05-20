<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Classes\CalendarEvent;
use Ilios\CoreBundle\Classes\UserEvent;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Entity\DTO\UserDTO;

/**
 * Class UserManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class UserManager extends DTOManager
{
    use Manageable;

    /**
     * @deprecated
     */
    public function findUserBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findUserDTOBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findDTOBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findUsersBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return UserDTO[]
     */
    public function findUserDTOsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
    
        return $this->findDTOsBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param array $campusIds
     *
     * @return UserDTO[]
     */
    public function findAllMatchingDTOsByCampusIds(
        array $campusIds
    ) {
        return $this->getRepository()->findAllMatchingDTOsByCampusIds($campusIds);
    }

    /**
     * @deprecated
     */
    public function updateUser(
        UserInterface $user,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($user, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteUser(
        UserInterface $user
    ) {
        $this->delete($user);
    }

    /**
     * @deprecated
     */
    public function createUser()
    {
        return $this->create();
    }

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
    ) {
        return $this->getRepository()->findByQ($q, $orderBy, $limit, $offset, $criteria);
    }

    /**
     * Find all of the events for a user id between two dates.
     *
     * @param integer $userId
     * @param \DateTime $from
     * @param \DateTime $to
     * @return UserEvent[]
     */
    public function findEventsForUser($userId, \DateTime $from, \DateTime $to)
    {
        return $this->getRepository()->findEventsForUser($userId, $from, $to);
    }

    /**
     * Finds and adds instructors to a given list of calendar events.
     *
     * @param CalendarEvent[] $events
     * @return CalendarEvent[]
     */
    public function addInstructorsToEvents(array $events)
    {
        return $this->getRepository()->addInstructorsToEvents($events);
    }

    /**
     * @param array $campusIdFilter an array of the campusIDs to include in our search if empty then all users
     *
     * @return ArrayCollection
     */
    public function findUsersWhoAreNotFormerStudents(array $campusIdFilter = array())
    {
        return $this->getRepository()->findUsersWhoAreNotFormerStudents($campusIdFilter);
    }

    /**
     * Get all the campus IDs for every user
     * @param $includeDisabled
     * @param $includeSyncIgnore
     *
     * @return array
     */
    public function getAllCampusIds($includeDisabled = true, $includeSyncIgnore = true)
    {
        return $this->getRepository()->getAllCampusIds($includeDisabled, $includeSyncIgnore);
        
    }

    /**
     * Reset the examined flags on every user
     */
    public function resetExaminedFlagForAllUsers()
    {
        return $this->getRepository()->resetExaminedFlagForAllUsers();
    }
}
