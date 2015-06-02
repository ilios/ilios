<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\PermissionInterface;

/**
 * Class PermissionManager
 * @package Ilios\CoreBundle\Entity\Manager
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
     * @return PermissionInterface
     */
    public function findPermissionBy(
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
     * @return ArrayCollection|PermissionInterface[]
     */
    public function findPermissionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param PermissionInterface $permission
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updatePermission(
        PermissionInterface $permission,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($permission);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($permission));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param PermissionInterface $permission
     */
    public function deletePermission(
        PermissionInterface $permission
    ) {
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
