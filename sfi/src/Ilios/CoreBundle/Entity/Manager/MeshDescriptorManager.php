<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\MeshDescriptorManager as BaseMeshDescriptorManager;
use Ilios\CoreBundle\Model\MeshDescriptorInterface;

class MeshDescriptorManager extends BaseMeshDescriptorManager
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
     * Previously known as findAllBy()
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
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
     *
     * @return void
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
     *
     * @return void
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
}
