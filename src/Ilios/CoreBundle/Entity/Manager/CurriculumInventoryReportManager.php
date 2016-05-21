<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;

/**
 * Class CurriculumInventoryReportManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CurriculumInventoryReportManager extends AbstractManager implements CurriculumInventoryReportManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findCurriculumInventoryReportBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findCurriculumInventoryReportsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function updateCurriculumInventoryReport(
        CurriculumInventoryReportInterface $curriculumInventoryReport,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($curriculumInventoryReport);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($curriculumInventoryReport));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCurriculumInventoryReport(
        CurriculumInventoryReportInterface $curriculumInventoryReport
    ) {
        $this->em->remove($curriculumInventoryReport);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createCurriculumInventoryReport()
    {
        $class = $this->getClass();
        return new $class();
    }

    /**
     * {@inheritdoc}
     */
    public function getEvents(CurriculumInventoryReportInterface $report)
    {
        return $this->getRepository()->getEvents($report);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventResourceTypes(CurriculumInventoryReportInterface $report)
    {
        return $this->getRepository()->getEventResourceTypes($report);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventKeywords(CurriculumInventoryReportInterface $report)
    {
        return $this->getRepository()->getEventKeywords($report);
    }

    /**
     * {@inheritdoc}
     */
    public function getEventReferencesForSequenceBlocks(CurriculumInventoryReportInterface $report)
    {
        return $this->getRepository()->getEventReferencesForSequenceBlocks($report);
    }

    /**
     * {@inheritdoc}
     */
    public function getProgramObjectives(CurriculumInventoryReportInterface $report)
    {
        return $this->getRepository()->getProgramObjectives($report);
    }

    /**
     * {@inheritdoc}
     */
    public function getSessionObjectives(CurriculumInventoryReportInterface $report)
    {
        return $this->getRepository()->getSessionObjectives($report);
    }

    /**
     * {@inheritdoc}
     */
    public function getCourseObjectives(CurriculumInventoryReportInterface $report)
    {
        return $this->getRepository()->getCourseObjectives($report);
    }

    /**
     * {@inheritdoc}
     */
    public function getPcrs(CurriculumInventoryReportInterface $report)
    {
        return $this->getRepository()->getPcrs($report);
    }

    /**
     * {@inheritdoc}
     */
    public function getCompetencyObjectReferencesForSequenceBlocks(CurriculumInventoryReportInterface $report)
    {
        return $this->getRepository()->getCompetencyObjectReferencesForSequenceBlocks($report);
    }

    /**
     * {@inheritdoc}
     */
    public function getCompetencyObjectReferencesForEvents(CurriculumInventoryReportInterface $report)
    {
        return $this->getRepository()->getCompetencyObjectReferencesForEvents($report);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getCourseObjectivesToProgramObjectivesRelations(
        array $courseObjectiveIds,
        array $programObjectiveIds
    ) {
        return $this->getRepository()->getCourseObjectivesToProgramObjectivesRelations(
            $courseObjectiveIds,
            $programObjectiveIds
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getProgramObjectivesToPcrsRelations(array $programObjectiveIds, array $pcrsIds)
    {
        return $this->getRepository()->getProgramObjectivesToPcrsRelations(
            $programObjectiveIds,
            $pcrsIds
        );
    }
}
