<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSessionInterface;

/**
 * Class CurriculumInventorySequenceBlockSessionManager
 * @package Ilios\CoreBundle\Entity\Manager
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
     * @return CurriculumInventorySequenceBlockSessionInterface
     */
    public function findCurriculumInventorySequenceBlockSessionBy(
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
     * @return ArrayCollection|CurriculumInventorySequenceBlockSessionInterface[]
     */
    public function findCurriculumInventorySequenceBlockSessionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateCurriculumInventorySequenceBlockSession(
        CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($curriculumInventorySequenceBlockSession);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($curriculumInventorySequenceBlockSession));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
     */
    public function deleteCurriculumInventorySequenceBlockSession(
        CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
    ) {
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
