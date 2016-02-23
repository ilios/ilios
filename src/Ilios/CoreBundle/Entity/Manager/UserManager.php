<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Entity\DTO\UserDTO;

/**
 * Class UserManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class UserManager extends AbstractManager implements UserManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findUserBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * @inheritdoc
     */
    public function findUserDTOBy(
        array $criteria,
        array $orderBy = null
    ) {
        $results = $this->getRepository()->findDTOsBy($criteria, $orderBy, 1);
        return empty($results) ? false : $results[0];
    }

    /**
     * {@inheritdoc}
     */
    public function findUsersBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function findUserDTOsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
    
        return $this->getRepository()->findDTOsBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function findAllMatchingDTOsByCampusIds(
        array $campusIds
    ) {
        return $this->getRepository()->findAllMatchingDTOsByCampusIds($campusIds);
    }

    /**
     * {@inheritdoc}
     */
    public function updateUser(
        UserInterface $user,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($user);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($user));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteUser(
        UserInterface $user
    ) {
        $this->em->remove($user);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createUser()
    {
        $class = $this->getClass();
        return new $class();
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function findEventsForUser($userId, \DateTime $from, \DateTime $to)
    {
        return $this->getRepository()->findEventsForUser($userId, $from, $to);
    }

    /**
     * @inheritdoc
     */
    public function addInstructorsToEvents(array $events)
    {
        return $this->getRepository()->addInstructorsToEvents($events);
    }

    /**
     * {@inheritdoc}
     */
    public function findUsersWhoAreNotFormerStudents(array $campusIdFilter = array())
    {
        return $this->getRepository()->findUsersWhoAreNotFormerStudents($campusIdFilter);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllCampusIds($includeDisabled = true, $includeSyncIgnore = true)
    {
        return $this->getRepository()->getAllCampusIds($includeDisabled, $includeSyncIgnore);
        
    }

    /**
     * {@inheritdoc}
     */
    public function resetExaminedFlagForAllUsers()
    {
        return $this->getRepository()->resetExaminedFlagForAllUsers();
    }
}
