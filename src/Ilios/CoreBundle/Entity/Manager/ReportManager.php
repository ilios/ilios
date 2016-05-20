<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\ReportInterface;

/**
 * Class ReportManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class ReportManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findReportBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findReportsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateReport(
        ReportInterface $report,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($report, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteReport(
        ReportInterface $report
    ) {
        $this->delete($report);
    }

    /**
     * @deprecated
     */
    public function createReport()
    {
        return $this->create();
    }
}
