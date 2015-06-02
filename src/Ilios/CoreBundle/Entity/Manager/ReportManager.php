<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\ReportInterface;

/**
 * Class ReportManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class ReportManager implements ReportManagerInterface
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
     * @return ReportInterface
     */
    public function findReportBy(
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
     * @return ArrayCollection|ReportInterface[]
     */
    public function findReportsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param ReportInterface $report
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateReport(
        ReportInterface $report,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($report);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($report));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param ReportInterface $report
     */
    public function deleteReport(
        ReportInterface $report
    ) {
        $this->em->remove($report);
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
     * @return ReportInterface
     */
    public function createReport()
    {
        $class = $this->getClass();
        return new $class();
    }
}
