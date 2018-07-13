<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;
use Ilios\CoreBundle\Entity\Repository\CurriculumInventoryReportRepository;

/**
 * Class CurriculumInventoryReportManager
 */
class CurriculumInventoryReportManager extends BaseManager
{
    /**
     * @param CurriculumInventoryReportInterface $report
     * @return array
     * @throws \Exception
     */
    public function getEvents(CurriculumInventoryReportInterface $report)
    {
        /** @var CurriculumInventoryReportRepository $repo */
        $repo = $this->getRepository();
        return $repo->getEvents($report);
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @param array|int[] $eventIds
     * @return array
     * @throws \Exception
     */
    public function getEventResourceTypes(CurriculumInventoryReportInterface $report, array $eventIds = array())
    {
        /** @var CurriculumInventoryReportRepository $repo */
        $repo = $this->getRepository();
        return $repo->getEventResourceTypes($report, $eventIds);
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @param array|int[] $eventIds
     * @return array
     * @throws \Exception
     */
    public function getEventKeywords(CurriculumInventoryReportInterface $report, array $eventIds = array())
    {
        /** @var CurriculumInventoryReportRepository $repo */
        $repo = $this->getRepository();
        return $repo->getEventKeywords($report, $eventIds);
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @param array|int[] $eventIds
     * @return array
     * @throws \Exception
     */
    public function getEventReferencesForSequenceBlocks(
        CurriculumInventoryReportInterface $report,
        array $eventIds = array()
    ) {
        /** @var CurriculumInventoryReportRepository $repo */
        $repo = $this->getRepository();
        return $repo->getEventReferencesForSequenceBlocks($report, $eventIds);
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @return array
     * @throws \Exception
     */
    public function getProgramObjectives(CurriculumInventoryReportInterface $report)
    {
        /** @var CurriculumInventoryReportRepository $repo */
        $repo = $this->getRepository();
        return $repo->getProgramObjectives($report);
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @param array|int[] $sessionIds
     * @return array
     * @throws \Exception
     */
    public function getSessionObjectives(CurriculumInventoryReportInterface $report, array $sessionIds = array())
    {
        /** @var CurriculumInventoryReportRepository $repo */
        $repo = $this->getRepository();
        return $repo->getSessionObjectives($report, $sessionIds);
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @return array
     * @throws \Exception
     */
    public function getCourseObjectives(CurriculumInventoryReportInterface $report)
    {
        /** @var CurriculumInventoryReportRepository $repo */
        $repo = $this->getRepository();
        return $repo->getCourseObjectives($report);
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @return array
     * @throws \Exception
     */
    public function getPcrs(CurriculumInventoryReportInterface $report)
    {
        /** @var CurriculumInventoryReportRepository $repo */
        $repo = $this->getRepository();
        return $repo->getPcrs($report);
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @param array|int[] $consolidatedProgramObjectivesMap
     * @return array
     * @throws \Exception
     */
    public function getCompetencyObjectReferencesForSequenceBlocks(
        CurriculumInventoryReportInterface $report,
        array $consolidatedProgramObjectivesMap
    ) {
        /** @var CurriculumInventoryReportRepository $repo */
        $repo = $this->getRepository();
        return $repo->getCompetencyObjectReferencesForSequenceBlocks(
            $report,
            $consolidatedProgramObjectivesMap
        );
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @param array|int[] $consolidatedProgramObjectivesMap
     * @param array|int[] $eventIds
     * @return array
     * @throws \Exception
     */
    public function getCompetencyObjectReferencesForEvents(
        CurriculumInventoryReportInterface $report,
        array $consolidatedProgramObjectivesMap,
        array $eventIds = array()
    ) {
        /** @var CurriculumInventoryReportRepository $repo */
        $repo = $this->getRepository();
        return $repo->getCompetencyObjectReferencesForEvents(
            $report,
            $consolidatedProgramObjectivesMap,
            $eventIds
        );
    }

    /**
     * @param array $sessionObjectiveIds
     * @param array $courseObjectiveIds
     * @return array
     * @throws \Exception
     */
    public function getSessionObjectivesToCourseObjectivesRelations(
        array $sessionObjectiveIds,
        array $courseObjectiveIds
    ) {
        /** @var CurriculumInventoryReportRepository $repo */
        $repo = $this->getRepository();
        return $repo->getSessionObjectivesToCourseObjectivesRelations(
            $sessionObjectiveIds,
            $courseObjectiveIds
        );
    }

    /**
     * @param array $courseObjectiveIds
     * @param array $programObjectiveIds
     * @param array $consolidatedProgramObjectivesMap
     * @return array
     * @throws \Exception
     */
    public function getCourseObjectivesToProgramObjectivesRelations(
        array $courseObjectiveIds,
        array $programObjectiveIds,
        array $consolidatedProgramObjectivesMap
    ) {
        /** @var CurriculumInventoryReportRepository $repo */
        $repo = $this->getRepository();
        return $repo->getCourseObjectivesToProgramObjectivesRelations(
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
     * @throws \Exception
     */
    public function getProgramObjectivesToPcrsRelations(
        array $programObjectiveIds,
        array $pcrsIds,
        array $consolidatedProgramObjectivesMap
    ) {
        /** @var CurriculumInventoryReportRepository $repo */
        $repo = $this->getRepository();
        return $repo->getProgramObjectivesToPcrsRelations(
            $programObjectiveIds,
            $pcrsIds,
            $consolidatedProgramObjectivesMap
        );
    }
}
