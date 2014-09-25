<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\PermissionManager as BasePermissionManager;
use Ilios\CoreBundle\Model\PermissionInterface;

class PermissionManager extends BasePermissionManager
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
     * Previously known as findAllBy()
     *
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
     *
     * @return void
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
     *
     * @return void
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
}
