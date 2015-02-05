<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\MeshConceptInterface;

/**
 * MeshConcept manager service.
 * Class MeshConceptManager
 * @package Ilios\CoreBundle\Manager
 */
class MeshConceptManager implements MeshConceptManagerInterface
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
     * @return MeshConceptInterface
     */
    public function findMeshConceptBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return MeshConceptInterface[]|Collection
     */
    public function findMeshConceptsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param MeshConceptInterface $meshConcept
     * @param bool $andFlush
     */
    public function updateMeshConcept(MeshConceptInterface $meshConcept, $andFlush = true)
    {
        $this->em->persist($meshConcept);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param MeshConceptInterface $meshConcept
     */
    public function deleteMeshConcept(MeshConceptInterface $meshConcept)
    {
        $this->em->remove($meshConcept);
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
     * @return MeshConceptInterface
     */
    public function createMeshConcept()
    {
        $class = $this->getClass();
        return new $class();
    }
}
