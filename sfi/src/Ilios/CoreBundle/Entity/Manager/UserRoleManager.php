<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\UserRoleManager as BaseUserRoleManager;
use Ilios\CoreBundle\Model\UserRoleInterface;

class UserRoleManager extends BaseUserRoleManager
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
     * @return UserRoleInterface
     */
    public function findUserRoleBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * Previously known as findAllBy()
     *
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return UserRoleInterface[]|Collection
     */
    public function findUserRolesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param UserRoleInterface $userRole
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateUserRole(UserRoleInterface $userRole, $andFlush = true)
    {
        $this->em->persist($userRole);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param UserRoleInterface $userRole
     *
     * @return void
     */
    public function deleteUserRole(UserRoleInterface $userRole)
    {
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
}
