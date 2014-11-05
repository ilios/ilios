<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\ReportInterface;

/**
 * Interface ReportManagerInterface
 */
interface ReportManagerInterface
{
    /** 
     *@return ReportInterface
     */
    public function createReport();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return ReportInterface
     */
    public function findReportBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return ReportInterface[]|Collection
     */
    public function findReportsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param ReportInterface $report
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateReport(ReportInterface $report, $andFlush = true);

    /**
     * @param ReportInterface $report
     *
     * @return void
     */
    public function deleteReport(ReportInterface $report);

    /**
     * @return string
     */
    public function getClass();
}
