<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\ReportInterface;

/**
 * Interface ReportManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface ReportManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return ReportInterface
     */
    public function findReportBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ReportInterface[]
     */
    public function findReportsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param ReportInterface $report
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateReport(
        ReportInterface $report,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param ReportInterface $report
     *
     * @return void
     */
    public function deleteReport(
        ReportInterface $report
    );

    /**
     * @return ReportInterface
     */
    public function createReport();
}
