<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\MeshTermInterface;

/**
 * MeshTerm manager service.
 * Class MeshTermManager
 * @package Ilios\CoreBundle\Manager
 */
class MeshTermManager implements MeshTermManagerInterface
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
     * @return MeshTermInterface
     */
    public function findMeshTermBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return MeshTermInterface[]|Collection
     */
    public function findMeshTermsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param MeshTermInterface $meshTerm
     * @param bool $andFlush
     */
    public function updateMeshTerm(MeshTermInterface $meshTerm, $andFlush = true)
    {
        $this->em->persist($meshTerm);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param MeshTermInterface $meshTerm
     */
    public function deleteMeshTerm(MeshTermInterface $meshTerm)
    {
        $this->em->remove($meshTerm);
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
     * @return MeshTermInterface
     */
    public function createMeshTerm()
    {
        $class = $this->getClass();
        return new $class();
    }
}
