<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * User manager service.
 * Class UserManager
 * @package Ilios\CoreBundle\Manager
 */
class UserManager implements UserManagerInterface
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
     * @return UserInterface
     */
    public function findUserBy(
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
     * @return UserInterface[]|Collection
     */
    public function findUsersBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param UserInterface $user
     * @param bool $andFlush
     */
    public function updateUser(
        UserInterface $user,
        $andFlush = true
    ) {
        $this->em->persist($user);
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
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return UserInterface
     */
    public function createUser()
    {
        $class = $this->getClass();
        return new $class();
    }
}
