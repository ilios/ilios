<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\MeshSemanticTypeManager as BaseMeshSemanticTypeManager;
use Ilios\CoreBundle\Model\MeshSemanticTypeInterface;

class MeshSemanticTypeManager extends BaseMeshSemanticTypeManager
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
     * Previously known as findAllBy()
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
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
     *
     * @return void
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
     *
     * @return void
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
}
