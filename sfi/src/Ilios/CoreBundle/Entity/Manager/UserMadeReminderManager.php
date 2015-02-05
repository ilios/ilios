<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\UserMadeReminderInterface;

/**
 * UserMadeReminder manager service.
 * Class UserMadeReminderManager
 * @package Ilios\CoreBundle\Manager
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
     * @param EntityManager $em
     * @param string $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em         = $em;
        $this->class      = $class;
        $this->repository = $em->getRepository($class);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return UserMadeReminderInterface
     */
    public function findUserMadeReminderBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return UserMadeReminderInterface[]|Collection
     */
    public function findUserMadeRemindersBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param UserMadeReminderInterface $userMadeReminder
     * @param bool $andFlush
     */
    public function updateUserMadeReminder(UserMadeReminderInterface $userMadeReminder, $andFlush = true)
    {
        $this->em->persist($userMadeReminder);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param UserMadeReminderInterface $userMadeReminder
     */
    public function deleteUserMadeReminder(UserMadeReminderInterface $userMadeReminder)
    {
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
