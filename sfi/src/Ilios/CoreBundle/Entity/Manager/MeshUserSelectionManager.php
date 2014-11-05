<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\MeshUserSelectionManager as BaseMeshUserSelectionManager;
use Ilios\CoreBundle\Model\MeshUserSelectionInterface;

class MeshUserSelectionManager extends BaseMeshUserSelectionManager
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
    public function findMeshUserSelectionBy(array $criteria, array $orderBy = null)
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
     * @return MeshUserSelectionInterface[]|Collection
     */
    public function findMeshUserSelectionsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param MeshUserSelectionInterface $meshUserSelection
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateMeshUserSelection(MeshUserSelectionInterface $meshUserSelection, $andFlush = true)
    {
        $this->em->persist($meshUserSelection);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param MeshUserSelectionInterface $meshUserSelection
     *
     * @return void
     */
    public function deleteMeshUserSelection(MeshUserSelectionInterface $meshUserSelection)
    {
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
}
