<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\MeshConceptManager as BaseMeshConceptManager;
use Ilios\CoreBundle\Model\MeshConceptInterface;

class MeshConceptManager extends BaseMeshConceptManager
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
     * Previously known as findAllBy()
     *
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
     *
     * @return void
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
     *
     * @return void
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
}
