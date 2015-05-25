<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\MeshUserSelectionInterface;

/**
 * Class MeshUserSelectionManager
 * @package Ilios\CoreBundle\Entity\Manager
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
     * @return ArrayCollection|MeshUserSelectionInterface[]
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
     * @param bool $forceId
     */
    public function updateMeshUserSelection(
        MeshUserSelectionInterface $meshUserSelection,
        $andFlush = true,
        $forceId  = false
    ) {
        $this->em->persist($meshUserSelection);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($meshUserSelection));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

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
