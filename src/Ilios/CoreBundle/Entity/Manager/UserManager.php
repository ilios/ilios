<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class UserManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class UserManager extends AbstractManager implements UserManagerInterface
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
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

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
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param UserInterface $user
     * @param bool $andFlush
     * @param bool $forceId
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
     * @param UserInterface $user
     */
    public function deleteUser(
        UserInterface $user
    ) {
        $this->em->remove($user);
        $this->em->flush();
    }

    /**
     * @return UserInterface
     */
    public function createUser()
    {
        $class = $this->getClass();
        return new $class();
    }

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
    ) {
        return $this->getRepository()->findByQ($q, $orderBy, $limit, $offset);
    }

    /**
     * @param integer $userId
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return UserEvent[]|Collection
     */
    public function findEventsForUser(
        $userId,
        \DateTime $from,
        \DateTime $to
    ) {
        return $this->getRepository()->findEventsForUser($userId, $from, $to);
    }

    /**
     * @inheritdoc
     */
    public function findUsersWhoAreNotFormerStudents(array $campusIdFilter = array())
    {
        return $this->getRepository()->findUsersWhoAreNotFormerStudents($campusIdFilter);
    }
    
    /**
     * @inheritdoc
     */
    public function getAllCampusIds($includeDisabled = true, $includeSyncIgnore = true)
    {
        return $this->getRepository()->getAllCampusIds($includeDisabled, $includeSyncIgnore);
        
    }
    
    /**
     * @inheritdoc
     */
    public function resetExaminedFlagForAllUsers()
    {
        return $this->getRepository()->resetExaminedFlagForAllUsers();
    }
}
