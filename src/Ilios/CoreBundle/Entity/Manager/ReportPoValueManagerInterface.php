<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\ReportPoValueInterface;

/**
 * Interface ReportPoValueManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface ReportPoValueManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return ReportPoValueInterface
     */
    public function findReportPoValueBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|ReportPoValueInterface[]
     */
    public function findReportPoValuesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param ReportPoValueInterface $reportPoValue
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateReportPoValue(
        ReportPoValueInterface $reportPoValue,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param ReportPoValueInterface $reportPoValue
     *
     * @return void
     */
    public function deleteReportPoValue(
        ReportPoValueInterface $reportPoValue
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return ReportPoValueInterface
     */
    public function createReportPoValue();
}
