<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\CurriculumInventoryReportInterface;

/**
 * Interface CurriculumInventoryReportManagerInterface
 */
interface CurriculumInventoryReportManagerInterface
{
    /** 
     *@return CurriculumInventoryReportInterface
     */
    public function createCurriculumInventoryReport();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CurriculumInventoryReportInterface
     */
    public function findCurriculumInventoryReportBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return CurriculumInventoryReportInterface[]|Collection
     */
    public function findCurriculumInventoryReportsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param CurriculumInventoryReportInterface $curriculumInventoryReport
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCurriculumInventoryReport(CurriculumInventoryReportInterface $curriculumInventoryReport, $andFlush = true);

    /**
     * @param CurriculumInventoryReportInterface $curriculumInventoryReport
     *
     * @return void
     */
    public function deleteCurriculumInventoryReport(CurriculumInventoryReportInterface $curriculumInventoryReport);

    /**
     * @return string
     */
    public function getClass();
}
