<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\MeshConceptInterface;

/**
 * Class MeshConceptManager
 * @package Ilios\CoreBundle\Entity\Manager
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
     * @return MeshConceptInterface
     */
    public function findMeshConceptBy(
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
     * @return ArrayCollection|MeshConceptInterface[]
     */
    public function findMeshConceptsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param MeshConceptInterface $meshConcept
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateMeshConcept(
        MeshConceptInterface $meshConcept,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($meshConcept);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($meshConcept));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param MeshConceptInterface $meshConcept
     */
    public function deleteMeshConcept(
        MeshConceptInterface $meshConcept
    ) {
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
