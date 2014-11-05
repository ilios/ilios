<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\MeshQualifierManager as BaseMeshQualifierManager;
use Ilios\CoreBundle\Model\MeshQualifierInterface;

class MeshQualifierManager extends BaseMeshQualifierManager
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
     * @return MeshQualifierInterface
     */
    public function findMeshQualifierBy(array $criteria, array $orderBy = null)
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
     * @return MeshQualifierInterface[]|Collection
     */
    public function findMeshQualifiersBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param MeshQualifierInterface $meshQualifier
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateMeshQualifier(MeshQualifierInterface $meshQualifier, $andFlush = true)
    {
        $this->em->persist($meshQualifier);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param MeshQualifierInterface $meshQualifier
     *
     * @return void
     */
    public function deleteMeshQualifier(MeshQualifierInterface $meshQualifier)
    {
        $this->em->remove($meshQualifier);
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
