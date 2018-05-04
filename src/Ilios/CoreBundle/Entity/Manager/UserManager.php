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
     * @throws \Exception
     */
    public function findAllMatchingDTOsByCampusIds(
        array $campusIds
    ) {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->findAllMatchingDTOsByCampusIds($campusIds);
    }

    /**
     * @param string $q
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     * @param array $criteria
     *
     * @return UserInterface[]
     * @throws \Exception
     */
    public function findUsersByQ(
        $q,
        array $orderBy = null,
        $limit = null,
        $offset = null,
        array $criteria = array()
    ) {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->findByQ($q, $orderBy, $limit, $offset, $criteria);
    }

    /**
     * Find all of the events for a user id between two dates.
     *
     * @param integer $userId
     * @param \DateTime $from
     * @param \DateTime $to
     * @return UserEvent[]
     * @throws \Exception
     */
    public function findEventsForUser($userId, \DateTime $from, \DateTime $to)
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->findEventsForUser($userId, $from, $to);
    }

    /**
     * Finds and adds instructors to a given list of calendar events.
     *
     * @param CalendarEvent[] $events
     * @return CalendarEvent[]
     * @throws \Exception
     */
    public function addInstructorsToEvents(array $events)
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->addInstructorsToEvents($events);
    }

    /**
     * @param array $campusIdFilter an array of the campusIDs to include in our search if empty then all users
     *
     * @return ArrayCollection
     * @throws \Exception
     */
    public function findUsersWhoAreNotFormerStudents(array $campusIdFilter = array())
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->findUsersWhoAreNotFormerStudents($campusIdFilter);
    }

    /**
     * Get all the campus IDs for every user
     * @param $includeDisabled
     * @param $includeSyncIgnore
     *
     * @return array
     * @throws \Exception
     */
    public function getAllCampusIds($includeDisabled = true, $includeSyncIgnore = true)
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getAllCampusIds($includeDisabled, $includeSyncIgnore);
    }

    /**
     * Reset the examined flags on every user
     * @throws \Exception
     */
    public function resetExaminedFlagForAllUsers()
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->resetExaminedFlagForAllUsers();
    }

    /**
     * Find all of the learning materials for a userId
     *
     * @param integer $userId
     * @param array $criteria
     * @return UserMaterial[]
     * @throws \Exception
     */
    public function findMaterialsForUser($userId, $criteria)
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->findMaterialsForUser($userId, $this->factory, $criteria);
    }

    /**
     * Finds and adds learning materials to a given list of user events.
     *
     * @param UserEvent[] $events
     * @return UserEvent[]
     * @throws \Exception
     */
    public function addMaterialsToEvents(array $events)
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->addMaterialsToEvents($events, $this->factory);
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
     * @see UserRepository::getDirectedProgramYearProgramAndSchoolIds()
     */
    public function getDirectedProgramYearProgramAndSchoolIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getDirectedProgramYearProgramAndSchoolIds($userId);
    }

    /**
     * @param $userId
     * @return array
     * @throws \Exception
     * @see UserRepository::getLearnerGroupIds()
     */
    public function getLearnerGroupIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getLearnerGroupIds($userId);
    }
}
