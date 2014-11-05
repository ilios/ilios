<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\ReportPoValueManager as BaseReportPoValueManager;
use Ilios\CoreBundle\Model\ReportPoValueInterface;

class ReportPoValueManager extends BaseReportPoValueManager
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
    public function findReportPoValueBy(array $criteria, array $orderBy = null)
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
     * @return ReportPoValueInterface[]|Collection
     */
    public function findReportPoValuesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param ReportPoValueInterface $reportPoValue
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateReportPoValue(ReportPoValueInterface $reportPoValue, $andFlush = true)
    {
        $this->em->persist($reportPoValue);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param ReportPoValueInterface $reportPoValue
     *
     * @return void
     */
    public function deleteReportPoValue(ReportPoValueInterface $reportPoValue)
    {
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
}
