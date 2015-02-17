<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\ReportPoValueInterface;

/**
 * ReportPoValue manager service.
 * Class ReportPoValueManager
 * @package Ilios\CoreBundle\Manager
 */
class ReportPoValueManager implements ReportPoValueManagerInterface
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
     * @return ReportPoValueInterface
     */
    public function findReportPoValueBy(
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
     * @return ReportPoValueInterface[]|Collection
     */
    public function findReportPoValuesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param ReportPoValueInterface $reportPoValue
     * @param bool $andFlush
     */
    public function updateReportPoValue(
        ReportPoValueInterface $reportPoValue,
        $andFlush = true
    ) {
        $this->em->persist($reportPoValue);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param ReportPoValueInterface $reportPoValue
     */
    public function deleteReportPoValue(
        ReportPoValueInterface $reportPoValue
    ) {
        $this->em->remove($reportPoValue);
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
     * @return ReportPoValueInterface
     */
    public function createReportPoValue()
    {
        $class = $this->getClass();
        return new $class();
    }
}
