<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\MeshDescriptorInterface;

/**
 * Class MeshDescriptorManager
 * @package Ilios\CoreBundle\Entity\Manager
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
     * @return MeshDescriptorInterface
     */
    public function findMeshDescriptorBy(
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
     * @return ArrayCollection|MeshDescriptorInterface[]
     */
    public function findMeshDescriptorsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param MeshDescriptorInterface $meshDescriptor
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateMeshDescriptor(
        MeshDescriptorInterface $meshDescriptor,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($meshDescriptor);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($meshDescriptor));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param MeshDescriptorInterface $meshDescriptor
     */
    public function deleteMeshDescriptor(
        MeshDescriptorInterface $meshDescriptor
    ) {
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
