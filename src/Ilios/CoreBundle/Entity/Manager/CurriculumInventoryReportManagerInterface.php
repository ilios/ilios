<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\CurriculumInventoryReportEventInterface;
use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;

/**
 * Interface CurriculumInventoryReportManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface CurriculumInventoryReportManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CurriculumInventoryReportInterface
     */
    public function findCurriculumInventoryReportBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|CurriculumInventoryReportInterface[]
     */
    public function findCurriculumInventoryReportsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param CurriculumInventoryReportInterface $curriculumInventoryReport
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateCurriculumInventoryReport(
        CurriculumInventoryReportInterface $curriculumInventoryReport,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param CurriculumInventoryReportInterface $curriculumInventoryReport
     *
     * @return void
     */
    public function deleteCurriculumInventoryReport(
        CurriculumInventoryReportInterface $curriculumInventoryReport
    );

    /**
     * @return CurriculumInventoryReportInterface
     */
    public function createCurriculumInventoryReport();

    /**
     * Retrieves a list of events (derived from published sessions/offerings and independent learning sessions)
     * in a given curriculum inventory report.
     *
     * @param CurriculumInventoryReportInterface $report
     * @return CurriculumInventoryReportEventInterface[]
     */
    public function getEvents(CurriculumInventoryReportInterface $report);

    /**
     * @todo
     */
    public function getEventKeywords($reportId);

    /**
     * @todo
     */
    public function getEventReferencesForSequenceBlocks($reportId);

    /**
     * @todo
     */
    public function getProgramObjectives($reportId);

    /**
     * @todo
     */
    public function getSessionObjectives($reportId);

    /**
     * @todo
     */
    public function getCourseObjectives($reportId);

    /**
     * @todo
     */
    public function getPcrs($reportId);

    /**
     * @todo
     */
    public function getCompetencyObjectReferencesForSequenceBlocks($reportId);

    /**
     * @todo
     */
    public function getCompetencyObjectReferencesForEvents($reportId);
}
