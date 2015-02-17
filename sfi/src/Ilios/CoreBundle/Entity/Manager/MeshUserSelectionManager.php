<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\MeshUserSelectionInterface;

/**
 * MeshUserSelection manager service.
 * Class MeshUserSelectionManager
 * @package Ilios\CoreBundle\Manager
 */
class MeshUserSelectionManager implements MeshUserSelectionManagerInterface
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
     * @return MeshUserSelectionInterface
     */
    public function findMeshUserSelectionBy(
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
     * @return MeshUserSelectionInterface[]|Collection
     */
    public function findMeshUserSelectionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param MeshUserSelectionInterface $meshUserSelection
     * @param bool $andFlush
     */
    public function updateMeshUserSelection(
        MeshUserSelectionInterface $meshUserSelection,
        $andFlush = true
    ) {
        $this->em->persist($meshUserSelection);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param MeshUserSelectionInterface $meshUserSelection
     */
    public function deleteMeshUserSelection(
        MeshUserSelectionInterface $meshUserSelection
    ) {
        $this->em->remove($meshUserSelection);
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
     * @return MeshUserSelectionInterface
     */
    public function createMeshUserSelection()
    {
        $class = $this->getClass();
        return new $class();
    }
}
