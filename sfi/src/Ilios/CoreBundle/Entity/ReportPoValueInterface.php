<?php

namespace Ilios\CoreBundle\Entity;

/**
 * Interface ReportPoValueInterface
 * @package Ilios\CoreBundle\Entity
 */
interface ReportPoValueInterface
{
    /**
     * @param string $prepositionalObjectTableRowId
     */
    public function setPrepositionalObjectTableRowId($prepositionalObjectTableRowId);

    /**
     * @return string
     */
    public function getPrepositionalObjectTableRowId();

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted);

    /**
     * @return boolean
     */
    public function getDeleted();

    /**
     * @param ReportInterface $report
     */
    public function setReport(ReportInterface $report);

    /**
     * @return ReportInterface
     */
    public function getReport();
}