<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\MeshTreeXDescriptorManager as BaseMeshTreeXDescriptorManager;
use Ilios\CoreBundle\Model\MeshTreeXDescriptorInterface;

class MeshTreeXDescriptorManager extends BaseMeshTreeXDescriptorManager
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
     * @return MeshTreeXDescriptorInterface
     */
    public function findMeshTreeXDescriptorBy(array $criteria, array $orderBy = null)
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
     * @return MeshTreeXDescriptorInterface[]|Collection
     */
    public function findMeshTreeXDescriptorsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param MeshTreeXDescriptorInterface $meshTreeXDescriptor
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateMeshTreeXDescriptor(MeshTreeXDescriptorInterface $meshTreeXDescriptor, $andFlush = true)
    {
        $this->em->persist($meshTreeXDescriptor);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param MeshTreeXDescriptorInterface $meshTreeXDescriptor
     *
     * @return void
     */
    public function deleteMeshTreeXDescriptor(MeshTreeXDescriptorInterface $meshTreeXDescriptor)
    {
        $this->em->remove($meshTreeXDescriptor);
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
