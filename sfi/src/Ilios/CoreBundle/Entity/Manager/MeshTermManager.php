<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\MeshTermManager as BaseMeshTermManager;
use Ilios\CoreBundle\Model\MeshTermInterface;

class MeshTermManager extends BaseMeshTermManager
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
     * Previously known as findAllBy()
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
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
     *
     * @return void
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
     *
     * @return void
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
}
