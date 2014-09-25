<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\SessionLearningMaterialManager as BaseSessionLearningMaterialManager;
use Ilios\CoreBundle\Model\SessionLearningMaterialInterface;

class SessionLearningMaterialManager extends BaseSessionLearningMaterialManager
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
     * @return SessionLearningMaterialInterface
     */
    public function findSessionLearningMaterialBy(array $criteria, array $orderBy = null)
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
     * @return SessionLearningMaterialInterface[]|Collection
     */
    public function findSessionLearningMaterialsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial, $andFlush = true)
    {
        $this->em->persist($sessionLearningMaterial);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     *
     * @return void
     */
    public function deleteSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial)
    {
        $this->em->remove($sessionLearningMaterial);
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
