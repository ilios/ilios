<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;

/**
 * Class CurriculumInventoryReportManager
 */
class CurriculumInventoryReportManager extends BaseManager
{
    /**
     * @param CurriculumInventoryReportInterface $report
     * @return array
     */
    public function getEvents(CurriculumInventoryReportInterface $report)
    {
        return $this->getRepository()->getEvents($report);
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @param array|int[] $eventIds
     * @return array
     */
    public function getEventResourceTypes(CurriculumInventoryReportInterface $report, array $eventIds = array())
    {
        return $this->getRepository()->getEventResourceTypes($report, $eventIds);
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @param array|int[] $eventIds
     * @return array
     */
    public function getEventKeywords(CurriculumInventoryReportInterface $report, array $eventIds = array())
    {
        return $this->getRepository()->getEventKeywords($report, $eventIds);
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @param array|int[] $eventIds
     * @return array
     */
    public function getEventReferencesForSequenceBlocks(
        CurriculumInventoryReportInterface $report,
        array $eventIds = array()
    ) {
        return $this->getRepository()->getEventReferencesForSequenceBlocks($report, $eventIds);
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @return array
     */
    public function getProgramObjectives(CurriculumInventoryReportInterface $report)
    {
        return $this->getRepository()->getProgramObjectives($report);
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @param array|int[] $sessionIds
     * @return array
     */
    public function getSessionObjectives(CurriculumInventoryReportInterface $report, array $sessionIds = array())
    {
        return $this->getRepository()->getSessionObjectives($report, $sessionIds);
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @return array
     */
    public function getCourseObjectives(CurriculumInventoryReportInterface $report)
    {
        return $this->getRepository()->getCourseObjectives($report);
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @return array
     */
    public function getPcrs(CurriculumInventoryReportInterface $report)
    {
        return $this->getRepository()->getPcrs($report);
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @param array|int[] $consolidatedProgramObjectivesMap
     * @return array
     */
    public function getCompetencyObjectReferencesForSequenceBlocks(
        CurriculumInventoryReportInterface $report,
        array $consolidatedProgramObjectivesMap
    ) {
        return $this->getRepository()->getCompetencyObjectReferencesForSequenceBlocks(
            $report,
            $consolidatedProgramObjectivesMap
        );
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @param array|int[] $consolidatedProgramObjectivesMap
     * @param array|int[] $eventIds
     * @return array
     */
    public function getCompetencyObjectReferencesForEvents(
        CurriculumInventoryReportInterface $report,
        array $consolidatedProgramObjectivesMap,
        array $eventIds = array()
    ) {
        return $this->getRepository()->getCompetencyObjectReferencesForEvents(
            $report,
            $consolidatedProgramObjectivesMap,
            $eventIds
        );
    }

    /**
     * @param array $sessionObjectiveIds
     * @param array $courseObjectiveIds
     * @return array
     */
    public function getSessionObjectivesToCourseObjectivesRelations(
        array $sessionObjectiveIds,
        array $courseObjectiveIds
    ) {
        return $this->getRepository()->getSessionObjectivesToCourseObjectivesRelations(
            $sessionObjectiveIds,
            $courseObjectiveIds
        );
    }

    /**
     * @param array $courseObjectiveIds
     * @param array $programObjectiveIds
     * @param array $consolidatedProgramObjectivesMap
     * @return array
     */
    public function getCourseObjectivesToProgramObjectivesRelations(
        array $courseObjectiveIds,
        array $programObjectiveIds,
        array $consolidatedProgramObjectivesMap
    ) {
        return $this->getRepository()->getCourseObjectivesToProgramObjectivesRelations(
            $courseObjectiveIds,
            $programObjectiveIds,
            $consolidatedProgramObjectivesMap
        );
    }

    /**
     * @param array $programObjectiveIds
     * @param array $pcrsIds
     * @param array $consolidatedProgramObjectivesMap
     * @return array
     */
    public function getProgramObjectivesToPcrsRelations(
        array $programObjectiveIds,
        array $pcrsIds,
        array $consolidatedProgramObjectivesMap
    ) {
        return $this->getRepository()->getProgramObjectivesToPcrsRelations(
            $programObjectiveIds,
            $pcrsIds,
            $consolidatedProgramObjectivesMap
        );
    }
}
