<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceInterface;

/**
 * Class CurriculumInventorySequenceManager
 * @package Ilios\CoreBundle\Entity\Manager
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
     * @return ArrayCollection|CurriculumInventorySequenceInterface[]
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
     * @param bool $forceId
     */
    public function updateCurriculumInventorySequence(
        CurriculumInventorySequenceInterface $curriculumInventorySequence,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($curriculumInventorySequence);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($curriculumInventorySequence));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

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
