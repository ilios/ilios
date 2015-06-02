<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\UserRoleInterface;

/**
 * Class UserRoleManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class UserRoleManager implements UserRoleManagerInterface
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
     * @return UserRoleInterface
     */
    public function findUserRoleBy(
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
     * @return ArrayCollection|UserRoleInterface[]
     */
    public function findUserRolesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param UserRoleInterface $userRole
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateUserRole(
        UserRoleInterface $userRole,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($userRole);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($userRole));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param UserRoleInterface $userRole
     */
    public function deleteUserRole(
        UserRoleInterface $userRole
    ) {
        $this->em->remove($userRole);
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
     * @return UserRoleInterface
     */
    public function createUserRole()
    {
        $class = $this->getClass();
        return new $class();
    }
}
