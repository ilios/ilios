<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\MeshPreviousIndexingManager as BaseMeshPreviousIndexingManager;
use Ilios\CoreBundle\Model\MeshPreviousIndexingInterface;

class MeshPreviousIndexingManager extends BaseMeshPreviousIndexingManager
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
     * @return MeshPreviousIndexingInterface
     */
    public function findMeshPreviousIndexingBy(array $criteria, array $orderBy = null)
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
     * @return MeshPreviousIndexingInterface[]|Collection
     */
    public function findMeshPreviousIndexingsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param MeshPreviousIndexingInterface $meshPreviousIndexing
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateMeshPreviousIndexing(MeshPreviousIndexingInterface $meshPreviousIndexing, $andFlush = true)
    {
        $this->em->persist($meshPreviousIndexing);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param MeshPreviousIndexingInterface $meshPreviousIndexing
     *
     * @return void
     */
    public function deleteMeshPreviousIndexing(MeshPreviousIndexingInterface $meshPreviousIndexing)
    {
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
}
