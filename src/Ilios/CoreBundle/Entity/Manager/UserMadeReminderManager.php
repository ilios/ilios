<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\UserMadeReminderInterface;

/**
 * Class UserMadeReminderManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class UserMadeReminderManager implements UserMadeReminderManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param Registry $em
     * @param string $class
     */
    public function __construct(Registry $em, $class)
    {
        $this->em         = $em->getManagerForClass($class);
        $this->class      = $class;
        $this->repository = $em->getRepository($class);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return UserMadeReminderInterface
     */
    public function findUserMadeReminderBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|UserMadeReminderInterface[]
     */
    public function findUserMadeRemindersBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param UserMadeReminderInterface $userMadeReminder
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateUserMadeReminder(
        UserMadeReminderInterface $userMadeReminder,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($userMadeReminder);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($userMadeReminder));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param UserMadeReminderInterface $userMadeReminder
     */
    public function deleteUserMadeReminder(
        UserMadeReminderInterface $userMadeReminder
    ) {
        $this->em->remove($userMadeReminder);
        $this->em->flush();
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return UserMadeReminderInterface
     */
    public function createUserMadeReminder()
    {
        $class = $this->getClass();
        return new $class();
    }
}
