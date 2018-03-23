<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Classes\CalendarEvent;
use Ilios\CoreBundle\Classes\UserEvent;
use Ilios\CoreBundle\Classes\UserMaterial;
use Ilios\CoreBundle\Entity\Repository\UserRepository;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Entity\DTO\UserDTO;
use Ilios\CoreBundle\Service\UserMaterialFactory;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class UserManager
 */
class UserManager extends BaseManager
{
    /**
     * @var UserMaterialFactory
     */
    protected $factory;

    /**
     * @param RegistryInterface $registry
     * @param string $class
     * @param UserMaterialFactory $factory
     */
    public function __construct(RegistryInterface $registry, $class, UserMaterialFactory $factory)
    {
        parent::__construct($registry, $class);
        $this->factory = $factory;
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

    /**
     * Find all of the learning materials for a userId
     *
     * @param integer $userId
     * @param array $criteria
     * @return UserMaterial[]
     */
    public function findMaterialsForUser($userId, $criteria)
    {
        return $this->getRepository()->findMaterialsForUser($userId, $this->factory, $criteria);
    }

    /**
     * Finds and adds learning materials to a given list of user events.
     *
     * @param UserEvent[] $events
     * @return UserEvent[]
     */
    public function addMaterialsToEvents(array $events)
    {
        return $this->getRepository()->addMaterialsToEvents($events, $this->factory);
    }

    /**
     * Finds and adds learning materials to a given list of user events.
     *
     * @param int $userId
     * @return array
     */
    public function buildSessionRelationships(int $userId) : array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->buildSessionRelationships($userId);
    }

    /**
     * @param int $userId
     * @return array
     * @throws \Exception
     * @see UserRepository::getDirectedSchoolIds()
     */
    public function getDirectedSchoolIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getDirectedSchoolIds($userId);
    }

    /**
     * @param int $userId
     * @return array
     * @throws \Exception
     * @see UserRepository::getAdministeredSchoolIds()
     */
    public function getAdministeredSchoolIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getAdministeredSchoolIds($userId);
    }

    /**
     * @param int $userId
     * @return array
     * @throws \Exception
     * @see UserRepository::getDirectedCourseAndSchoolIds()
     */
    public function getDirectedCourseAndSchoolIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getDirectedCourseAndSchoolIds($userId);
    }

    /**
     * @param int $userId
     * @return array
     * @throws \Exception
     * @see UserRepository::getAdministeredCourseAndSchoolIds()
     */
    public function getAdministeredCourseAndSchoolIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getAdministeredCourseAndSchoolIds($userId);
    }

    /**
     * @param $userId
     * @return array
     * @throws \Exception
     * @see UserRepository::getAdministeredCurriculumInventoryReportAndSchoolIds
     */
    public function getAdministeredCurriculumInventoryReportAndSchoolIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getAdministeredCurriculumInventoryReportAndSchoolIds($userId);
    }

    /**
     * @param $userId
     * @return array
     * @throws \Exception
     * @see UserRepository::getAdministeredSessionCourseAndSchoolIds()
     */
    public function getAdministeredSessionCourseAndSchoolIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getAdministeredSessionCourseAndSchoolIds($userId);
    }

    /**
     * @param $userId
     * @return array
     * @throws \Exception
     * @see UserRepository::getInstructedLearnerGroupSchoolIds()
     */
    public function getInstructedLearnerGroupSchoolIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getInstructedLearnerGroupSchoolIds($userId);
    }

    /**
     * @param $userId
     * @return array
     * @throws \Exception
     * @see UserRepository::getInstructorGroupSchoolIds()
     */
    public function getInstructorGroupSchoolIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getInstructorGroupSchoolIds($userId);
    }

    /**
     * @param $userId
     * @return array
     * @throws \Exception
     * @see UserRepository::getInstructedSessionCourseAndSchoolIds()
     */
    public function getInstructedSessionCourseAndSchoolIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getInstructedSessionCourseAndSchoolIds($userId);
    }

    /**
     * @param $userId
     * @return array
     * @throws \Exception
     * @see UserRepository::getDirectedProgramAndSchoolIds()
     */
    public function getDirectedProgramAndSchoolIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getDirectedProgramAndSchoolIds($userId);
    }

    /**
     * @param $userId
     * @return array
     * @throws \Exception
     * @see UserRepository::getDirectedCohortProgramYearProgramAndSchoolIds()
     */
    public function getDirectedCohortProgramYearProgramAndSchoolIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getDirectedProgramAndSchoolIds($userId);
    }
}
