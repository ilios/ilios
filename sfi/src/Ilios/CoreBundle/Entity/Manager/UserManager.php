<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use Ilios\CoreBundle\Model\Manager\UserManager as BaseUserManager;
use Ilios\CoreBundle\Model\UserInterface;

/**
 * Class UserManager
 * @package Ilios\CoreBundle\Entity\Manager
 * @author Victor Passapera <vpassapera@gmail.com>
 */
class UserManager extends BaseUserManager
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
        $this->repository = $em->getRepository($class);
        $this->class      = $class;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param UserInterface $user
     * @return void
     */
    public function deleteUser(UserInterface $user)
    {
        $this->em->remove($user);
        $this->em->flush();
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @return UserInterface
     */
    public function findUserBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     * @return UserInterface[]
     */
    public function findUsersBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param UserInterface $user
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateUser(UserInterface $user, $andFlush = true)
    {
        $this->em->persist($user);
        if ($andFlush) {
            $this->em->flush();
        }
    }
}
