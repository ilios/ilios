<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\UserMadeReminderManager as BaseUserMadeReminderManager;
use Ilios\CoreBundle\Model\UserMadeReminderInterface;

class UserMadeReminderManager extends BaseUserMadeReminderManager
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
     * Previously known as findAllBy()
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
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
     *
     * @return void
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
     *
     * @return void
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
}
