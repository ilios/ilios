<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\MeshSemanticTypeInterface;

/**
 * MeshSemanticType manager service.
 * Class MeshSemanticTypeManager
 * @package Ilios\CoreBundle\Manager
 */
class MeshSemanticTypeManager implements MeshSemanticTypeManagerInterface
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
     * @return MeshSemanticTypeInterface
     */
    public function findMeshSemanticTypeBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return MeshSemanticTypeInterface[]|Collection
     */
    public function findMeshSemanticTypesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param MeshSemanticTypeInterface $meshSemanticType
     * @param bool $andFlush
     */
    public function updateMeshSemanticType(MeshSemanticTypeInterface $meshSemanticType, $andFlush = true)
    {
        $this->em->persist($meshSemanticType);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param MeshSemanticTypeInterface $meshSemanticType
     */
    public function deleteMeshSemanticType(MeshSemanticTypeInterface $meshSemanticType)
    {
        $this->em->remove($meshSemanticType);
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
     * @return MeshSemanticTypeInterface
     */
    public function createMeshSemanticType()
    {
        $class = $this->getClass();
        return new $class();
    }
}
