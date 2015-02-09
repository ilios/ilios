<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceInterface;

/**
 * CurriculumInventorySequence manager service.
 * Class CurriculumInventorySequenceManager
 * @package Ilios\CoreBundle\Manager
 */
class CurriculumInventorySequenceManager implements CurriculumInventorySequenceManagerInterface
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
     * @return CurriculumInventorySequenceInterface
     */
    public function findCurriculumInventorySequenceBy(
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
     * @return CurriculumInventorySequenceInterface[]|Collection
     */
    public function findCurriculumInventorySequencesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CurriculumInventorySequenceInterface $curriculumInventorySequence
     * @param bool $andFlush
     */
    public function updateCurriculumInventorySequence(
        CurriculumInventorySequenceInterface $curriculumInventorySequence,
        $andFlush = true
    ) {
        $this->em->persist($curriculumInventorySequence);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CurriculumInventorySequenceInterface $curriculumInventorySequence
     */
    public function deleteCurriculumInventorySequence(
        CurriculumInventorySequenceInterface $curriculumInventorySequence
    ) {
        $this->em->remove($curriculumInventorySequence);
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
     * @return CurriculumInventorySequenceInterface
     */
    public function createCurriculumInventorySequence()
    {
        $class = $this->getClass();
        return new $class();
    }
}
