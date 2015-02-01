<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSessionInterface;

/**
 * CurriculumInventorySequenceBlockSession manager service.
 * Class CurriculumInventorySequenceBlockSessionManager
 * @package Ilios\CoreBundle\Manager
 */
class CurriculumInventorySequenceBlockSessionManager implements CurriculumInventorySequenceBlockSessionManagerInterface
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
     * @return CurriculumInventorySequenceBlockSessionInterface
     */
    public function findCurriculumInventorySequenceBlockSessionBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return CurriculumInventorySequenceBlockSessionInterface[]|Collection
     */
    public function findCurriculumInventorySequenceBlockSessionsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
     * @param bool $andFlush
     */
    public function updateCurriculumInventorySequenceBlockSession(CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession, $andFlush = true)
    {
        $this->em->persist($curriculumInventorySequenceBlockSession);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
     */
    public function deleteCurriculumInventorySequenceBlockSession(CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession)
    {
        $this->em->remove($curriculumInventorySequenceBlockSession);
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
     * @return CurriculumInventorySequenceBlockSessionInterface
     */
    public function createCurriculumInventorySequenceBlockSession()
    {
        $class = $this->getClass();
        return new $class();
    }
}
