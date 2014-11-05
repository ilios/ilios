<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\MeshConceptXTermManager as BaseMeshConceptXTermManager;
use Ilios\CoreBundle\Model\MeshConceptXTermInterface;

class MeshConceptXTermManager extends BaseMeshConceptXTermManager
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
     * @return MeshConceptXTermInterface
     */
    public function findMeshConceptXTermBy(array $criteria, array $orderBy = null)
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
     * @return MeshConceptXTermInterface[]|Collection
     */
    public function findMeshConceptXTermsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param MeshConceptXTermInterface $meshConceptXTerm
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateMeshConceptXTerm(MeshConceptXTermInterface $meshConceptXTerm, $andFlush = true)
    {
        $this->em->persist($meshConceptXTerm);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param MeshConceptXTermInterface $meshConceptXTerm
     *
     * @return void
     */
    public function deleteMeshConceptXTerm(MeshConceptXTermInterface $meshConceptXTerm)
    {
        $this->em->remove($meshConceptXTerm);
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
