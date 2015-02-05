<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\MeshDescriptorInterface;

/**
 * MeshDescriptor manager service.
 * Class MeshDescriptorManager
 * @package Ilios\CoreBundle\Manager
 */
class MeshDescriptorManager implements MeshDescriptorManagerInterface
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
     * @return MeshDescriptorInterface
     */
    public function findMeshDescriptorBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return MeshDescriptorInterface[]|Collection
     */
    public function findMeshDescriptorsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param MeshDescriptorInterface $meshDescriptor
     * @param bool $andFlush
     */
    public function updateMeshDescriptor(MeshDescriptorInterface $meshDescriptor, $andFlush = true)
    {
        $this->em->persist($meshDescriptor);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param MeshDescriptorInterface $meshDescriptor
     */
    public function deleteMeshDescriptor(MeshDescriptorInterface $meshDescriptor)
    {
        $this->em->remove($meshDescriptor);
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
     * @return MeshDescriptorInterface
     */
    public function createMeshDescriptor()
    {
        $class = $this->getClass();
        return new $class();
    }
}
