<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\PermissionInterface;

/**
 * Permission manager service.
 * Class PermissionManager
 * @package Ilios\CoreBundle\Manager
 */
class PermissionManager implements PermissionManagerInterface
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
     * @return PermissionInterface
     */
    public function findPermissionBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return PermissionInterface[]|Collection
     */
    public function findPermissionsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param PermissionInterface $permission
     * @param bool $andFlush
     */
    public function updatePermission(PermissionInterface $permission, $andFlush = true)
    {
        $this->em->persist($permission);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param PermissionInterface $permission
     */
    public function deletePermission(PermissionInterface $permission)
    {
        $this->em->remove($permission);
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
     * @return PermissionInterface
     */
    public function createPermission()
    {
        $class = $this->getClass();
        return new $class();
    }
}
