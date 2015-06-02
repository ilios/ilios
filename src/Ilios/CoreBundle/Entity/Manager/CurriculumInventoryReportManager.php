<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;

/**
 * Class CurriculumInventoryReportManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CurriculumInventoryReportManager implements CurriculumInventoryReportManagerInterface
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
     * @return CurriculumInventoryReportInterface
     */
    public function findCurriculumInventoryReportBy(
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
     * @return ArrayCollection|CurriculumInventoryReportInterface[]
     */
    public function findCurriculumInventoryReportsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CurriculumInventoryReportInterface $curriculumInventoryReport
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateCurriculumInventoryReport(
        CurriculumInventoryReportInterface $curriculumInventoryReport,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($curriculumInventoryReport);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($curriculumInventoryReport));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CurriculumInventoryReportInterface $curriculumInventoryReport
     */
    public function deleteCurriculumInventoryReport(
        CurriculumInventoryReportInterface $curriculumInventoryReport
    ) {
        $this->em->remove($curriculumInventoryReport);
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
     * @return CurriculumInventoryReportInterface
     */
    public function createCurriculumInventoryReport()
    {
        $class = $this->getClass();
        return new $class();
    }
}
