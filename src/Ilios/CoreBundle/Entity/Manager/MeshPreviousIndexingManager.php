<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\MeshPreviousIndexingInterface;

/**
 * Class MeshPreviousIndexingManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class MeshPreviousIndexingManager implements MeshPreviousIndexingManagerInterface
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
     * @return MeshPreviousIndexingInterface
     */
    public function findMeshPreviousIndexingBy(
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
     * @return ArrayCollection|MeshPreviousIndexingInterface[]
     */
    public function findMeshPreviousIndexingsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param MeshPreviousIndexingInterface $meshPreviousIndexing
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateMeshPreviousIndexing(
        MeshPreviousIndexingInterface $meshPreviousIndexing,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($meshPreviousIndexing);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($meshPreviousIndexing));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param MeshPreviousIndexingInterface $meshPreviousIndexing
     */
    public function deleteMeshPreviousIndexing(
        MeshPreviousIndexingInterface $meshPreviousIndexing
    ) {
        $this->em->remove($meshPreviousIndexing);
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
     * @return MeshPreviousIndexingInterface
     */
    public function createMeshPreviousIndexing()
    {
        $class = $this->getClass();
        return new $class();
    }
}
