<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\ReportPoValueInterface;

/**
 * Interface ReportPoValueManagerInterface
 */
interface ReportPoValueManagerInterface
{
    /** 
     *@return ReportPoValueInterface
     */
    public function createReportPoValue();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return ReportPoValueInterface
     */
    public function findReportPoValueBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return ReportPoValueInterface[]|Collection
     */
    public function findReportPoValuesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param ReportPoValueInterface $reportPoValue
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateReportPoValue(ReportPoValueInterface $reportPoValue, $andFlush = true);

    /**
     * @param ReportPoValueInterface $reportPoValue
     *
     * @return void
     */
    public function deleteReportPoValue(ReportPoValueInterface $reportPoValue);

    /**
     * @return string
     */
    public function getClass();
}
