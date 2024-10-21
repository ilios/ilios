<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CourseObjective;
use App\Entity\CurriculumInventoryReport;
use App\Entity\ProgramYearObjective;
use App\Entity\Session;
use App\Entity\SessionObjective;
use App\Service\DTOCacheManager;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\DTO\CurriculumInventoryReportDTO;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\CurriculumInventoryReportInterface;

use function array_values;
use function array_keys;

class CurriculumInventoryReportRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface
{
    use ManagerRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected DTOCacheManager $cacheManager,
    ) {
        parent::__construct($registry, CurriculumInventoryReport::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()->select('x')
            ->distinct()->from(CurriculumInventoryReport::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);

        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new CurriculumInventoryReportDTO(
                $arr['id'],
                $arr['name'],
                $arr['description'],
                $arr['year'],
                $arr['startDate'],
                $arr['endDate'],
                $arr['token']
            );
        }
        $curriculumInventoryReportIds = array_keys($dtos);

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(
                'x.id as xId, ' .
                'export.id AS exportId, sequence.id AS sequenceId, program.id AS programId, ' .
                'school.id AS schoolId'
            )
            ->from(CurriculumInventoryReport::class, 'x')
            ->join('x.program', 'program')
            ->join('program.school', 'school')
            ->leftJoin('x.sequence', 'sequence')
            ->leftJoin('x.export', 'export')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $curriculumInventoryReportIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['xId']]->export = $arr['exportId'] ? (int)$arr['exportId'] : null;
            $dtos[$arr['xId']]->sequence = $arr['sequenceId'] ? (int)$arr['sequenceId'] : null;
            $dtos[$arr['xId']]->program = $arr['programId'] ? (int)$arr['programId'] : null;
            $dtos[$arr['xId']]->school = $arr['schoolId'] ? (int)$arr['schoolId'] : null;
        }

        $dtos = $this->attachRelatedToDtos(
            $dtos,
            [
                'sequenceBlocks',
                'academicLevels',
                'administrators',
            ],
        );

        return array_values($dtos);
    }


    protected function attachCriteriaToQueryBuilder(
        QueryBuilder $qb,
        array $criteria,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): void {
        if (array_key_exists('sequenceBlocks', $criteria)) {
            $ids = is_array($criteria['sequenceBlocks']) ? $criteria['sequenceBlocks'] : [$criteria['sequenceBlocks']];
            $qb->join('x.sequenceBlocks', 'sb');
            $qb->andWhere($qb->expr()->in('sb.id', ':sequenceBlocks'));
            $qb->setParameter(':sequenceBlocks', $ids);
        }
        if (array_key_exists('academicLevels', $criteria)) {
            $ids = is_array($criteria['academicLevels']) ? $criteria['academicLevels'] : [$criteria['academicLevels']];
            $qb->join('x.academicLevels', 'al');
            $qb->andWhere($qb->expr()->in('al.id', ':academicLevels'));
            $qb->setParameter(':academicLevels', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['sequenceBlocks']);
        unset($criteria['academicLevels']);

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param array|int[] $eventIds
     */
    public function getEventResourceTypes(CurriculumInventoryReportInterface $report, array $eventIds = []): array
    {
        if (empty($eventIds)) {
            return [];
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('s.id AS event_id, art.id AS resource_type_id, art.title AS resource_type_title')
            ->distinct()
            ->from(Session::class, 's')
            ->join('s.course', 'c')
            ->join('c.sequenceBlocks', 'sb')
            ->join('sb.report', 'r')
            ->join('s.terms', 't')
            ->join('t.aamcResourceTypes', 'art')
            ->where($qb->expr()->eq('r.id', ':id'))
            ->andWhere($qb->expr()->in('s.id', ':eventIds'))
            ->setParameter('id', $report->getId())
            ->setParameter('eventIds', $eventIds);

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * @param array|int[] $eventIds
     */
    public function getEventKeywords(CurriculumInventoryReportInterface $report, array $eventIds = []): array
    {
        $rhett = [];

        if (empty($eventIds)) {
            return $rhett;
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("s.id AS event_id, md.id, 'MeSH' AS source, md.name")
            ->from(Session::class, 's')
            ->join('s.course', 'c')
            ->join('c.sequenceBlocks', 'sb')
            ->join('sb.report', 'r')
            ->join('s.meshDescriptors', 'md')
            ->where($qb->expr()->eq('r.id', ':id'))
            ->andWhere($qb->expr()->in('s.id', ':eventIds'))
            ->setParameter('id', $report->getId())
            ->setParameter('eventIds', $eventIds);

        $queries[] = $qb->getQuery();
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("s.id AS event_id, t.id, v.title AS source, t.title AS name")
            ->from(Session::class, 's')
            ->join('s.course', 'c')
            ->join('c.sequenceBlocks', 'sb')
            ->join('sb.report', 'r')
            ->join('s.terms', 't')
            ->join('t.vocabulary', 'v')
            ->where($qb->expr()->eq('r.id', ':id'))
            ->andWhere($qb->expr()->in('s.id', ':eventIds'))
            ->setParameter('id', $report->getId())
            ->setParameter('eventIds', $eventIds);

        $queries[] = $qb->getQuery();
        foreach ($queries as $query) {
            /** @var Query $query */
            $rhett = array_merge($rhett, $query->getResult(AbstractQuery::HYDRATE_ARRAY));
        }
        return $rhett;
    }

    /**
     * @param array|int[] $eventIds
     */
    public function getEventReferencesForSequenceBlocks(
        CurriculumInventoryReportInterface $report,
        array $eventIds = []
    ) {
        if (empty($eventIds)) {
            return [];
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('sb.id, s.id AS event_id, s.supplemental AS optional')
            ->from(Session::class, 's')
            ->join('s.course', 'c')
            ->join('c.sequenceBlocks', 'sb')
            ->join('sb.report', 'r')
            ->where($qb->expr()->eq('r.id', ':id'))
            ->andWhere($qb->expr()->in('s.id', ':eventIds'))
            ->setParameter('id', $report->getId())
            ->setParameter('eventIds', $eventIds);

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    public function getProgramObjectives(CurriculumInventoryReportInterface $report): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('o.id, o.title, a.id AS ancestor_id')
            ->distinct()
            ->from(CurriculumInventoryReport::class, 'r')
            ->join('r.program', 'p')
            ->join('p.school', 's')
            ->join('r.sequenceBlocks', 'sb')
            ->join('sb.course', 'c')
            ->join('c.cohorts', 'co')
            ->join('co.programYear', 'py')
            ->join('py.program', 'p2')
            ->join('p2.school', 's2')
            ->join('py.programYearObjectives', 'o')
            ->leftJoin('o.ancestor', 'a')
            ->where($qb->expr()->eq('s.id', 's2.id'))
            ->andWhere($qb->expr()->eq('r.id', ':id'))
            ->setParameter('id', $report->getId());

        $rows = $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
        $rhett = [];
        foreach ($rows as $row) {
            $rhett[$row['id']] = $row;
        }
        return $rhett;
    }

    public function getCourseObjectives(CurriculumInventoryReportInterface $report): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('o.id, o.title')
            ->distinct()
            ->from(CurriculumInventoryReport::class, 'r')
            ->join('r.program', 'p')
            ->join('r.sequenceBlocks', 'sb')
            ->join('sb.course', 'c')
            ->join('c.courseObjectives', 'o')
            ->where($qb->expr()->eq('r.id', ':id'))
            ->setParameter('id', $report->getId());

        $rows = $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
        $rhett = [];
        foreach ($rows as $row) {
            $rhett[$row['id']] = $row;
        }
        return $rhett;
    }

    /**
     * @param array|int[] $sessionIds
     */
    public function getSessionObjectives(CurriculumInventoryReportInterface $report, array $sessionIds = []): array
    {
        $rhett = [];

        if (empty($sessionIds)) {
            return $rhett;
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('o.id, o.title')
            ->distinct()
            ->from(CurriculumInventoryReport::class, 'r')
            ->join('r.program', 'p')
            ->join('r.sequenceBlocks', 'sb')
            ->join('sb.course', 'c')
            ->join('c.sessions', 's')
            ->join('s.sessionObjectives', 'o')
            ->where($qb->expr()->eq('r.id', ':id'))
            ->andWhere($qb->expr()->in('s.id', ':sessionIds'))
            ->setParameter('id', $report->getId())
            ->setParameter('sessionIds', $sessionIds);

        $rows = $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);

        foreach ($rows as $row) {
            $rhett[$row['id']] = $row;
        }
        return $rhett;
    }

    public function getCompetencyObjectReferencesForEvents(
        CurriculumInventoryReportInterface $report,
        array $eventIds = []
    ) {
        if (empty($eventIds)) {
            return [];
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(
            's.id AS event_id, so.id AS session_objective_id, co.id AS course_objective_id,'
            . 'po.id AS program_objective_id'
        )
            ->distinct()
            ->from(CurriculumInventoryReport::class, 'r')
            ->join('r.program', 'p')
            ->join('r.sequenceBlocks', 'sb')
            ->join('sb.course', 'c')
            ->join('c.sessions', 's')
            ->leftJoin('s.sessionObjectives', 'so')
            ->leftJoin('so.courseObjectives', 'co')
            ->leftJoin('co.programYearObjectives', 'po')
            ->where($qb->expr()->eq('r.id', ':id'))
            ->andWhere($qb->expr()->in('s.id', ':eventIds'))
            ->setParameter('id', $report->getId())
            ->setParameter('eventIds', $eventIds);

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    public function getCompetencyObjectReferencesForSequenceBlocks(CurriculumInventoryReportInterface $report): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('sb.id, co.id AS course_objective_id, po.id AS program_objective_id')
            ->distinct()
            ->from(CurriculumInventoryReport::class, 'r')
            ->join('r.program', 'p')
            ->join('p.school', 's')
            ->join('r.sequenceBlocks', 'sb')
            ->join('sb.course', 'c')
            ->leftJoin('c.courseObjectives', 'co')
            ->leftJoin('co.programYearObjectives', 'po')
            ->where($qb->expr()->eq('r.id', ':id'))
            ->setParameter('id', $report->getId());

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    public function getProgramObjectivesToPcrsRelations(array $programObjectivesId, array $pcrsIds): array
    {
        if ($programObjectivesId === [] || $pcrsIds === []) {
            return [];
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('o.id as objective_id, am.id AS pcrs_id')
            ->distinct()
            ->from(ProgramYearObjective::class, 'o')
            ->join('o.competency', 'c')
            ->join('c.aamcPcrses', 'am')
            ->where($qb->expr()->in('am.id', ':pcrs'))
            ->andWhere($qb->expr()->in('o.id', ':objectives'))
            ->setParameter(':pcrs', $pcrsIds)
            ->setParameter(':objectives', $programObjectivesId);

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }
    public function getCourseObjectivesToProgramObjectivesRelations(
        array $courseObjectiveIds,
        array $programObjectiveIds
    ) {
        if ($courseObjectiveIds === [] || $programObjectiveIds === []) {
            return [];
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('o.id AS objective_id, p.id AS program_objective_id')
            ->distinct()
            ->from(CourseObjective::class, 'o')
            ->join('o.programYearObjectives', 'p')
            ->where($qb->expr()->in('p.id', ':programObjectiveIds'))
            ->andWhere($qb->expr()->in('o.id', ':courseObjectiveIds'))
            ->setParameter(':courseObjectiveIds', $courseObjectiveIds)
            ->setParameter(':programObjectiveIds', $programObjectiveIds);

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    public function getSessionObjectivesToCourseObjectivesRelations(
        array $sessionObjectiveIds,
        array $courseObjectiveIds
    ) {
        if ($sessionObjectiveIds === [] || $courseObjectiveIds === []) {
            return [];
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('o.id AS objective_id, c.id AS course_objective_id')
            ->distinct()
            ->from(SessionObjective::class, 'o')
            ->join('o.courseObjectives', 'c')
            ->where($qb->expr()->in('c.id', ':courseObjectiveIds'))
            ->andWhere($qb->expr()->in('o.id', ':sessionObjectiveIds'))
            ->setParameter(':sessionObjectiveIds', $sessionObjectiveIds)
            ->setParameter(':courseObjectiveIds', $courseObjectiveIds);

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    public function getPcrs(CurriculumInventoryReportInterface $report): array
    {
        $rhett = [];
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('am.id AS pcrs_id, am.description')
            ->from(CurriculumInventoryReport::class, 'r')
            ->join('r.program', 'p')
            ->join('p.school', 's')
            ->join('r.sequenceBlocks', 'sb')
            ->join('sb.course', 'c')
            ->join('c.cohorts', 'co')
            ->join('co.programYear', 'py')
            ->join('py.programYearObjectives', 'pyxo')
            ->join('pyxo.competency', 'cm')
            ->join('cm.school', 's2')
            ->join('cm.parent', 'cm2')
            ->join('cm2.aamcPcrses', 'am')
            ->where($qb->expr()->eq('s.id', 's2.id'))
            ->andWhere($qb->expr()->eq('r.id', ':id'))
            ->setParameter(':id', $report->getId());
        $queries[] = $qb->getQuery();

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('am.id AS pcrs_id, am.description')
            ->from(CurriculumInventoryReport::class, 'r')
            ->join('r.program', 'p')
            ->join('p.school', 's')
            ->join('r.sequenceBlocks', 'sb')
            ->join('sb.course', 'c')
            ->join('c.cohorts', 'co')
            ->join('co.programYear', 'py')
            ->join('py.programYearObjectives', 'pyxo')
            ->join('pyxo.competency', 'cm')
            ->join('cm.school', 's2')
            ->join('cm.aamcPcrses', 'am')
            ->where($qb->expr()->eq('s.id', 's2.id'))
            ->andWhere($qb->expr()->eq('r.id', ':id'))
            ->setParameter(':id', $report->getId());
        $queries[] = $qb->getQuery();

        foreach ($queries as $query) {
            /** @var Query $query */
            $rows = $query->getResult(AbstractQuery::HYDRATE_ARRAY);
            foreach ($rows as $row) {
                $rhett[$row['pcrs_id']] = $row;
            }
        }
        return $rhett;
    }

    public function getEventsFromIlmOnlySessions(
        CurriculumInventoryReportInterface $report,
        array $excludedSessionIds = []
    ) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(
            's.id AS event_id, s.title, s.description, am.id AS method_id,'
            . 'st.assessment AS is_assessment_method, ao.name AS assessment_option_name, sf.hours'
        )
            ->from(Session::class, 's')
            ->join('s.course', 'c')
            ->join('s.ilmSession', 'sf')
            ->join('c.sequenceBlocks', 'sb')
            ->join('sb.report', 'r')
            ->leftJoin('s.offerings', 'o')
            ->leftJoin('s.sessionType', 'st')
            ->leftJoin('st.aamcMethods', 'am')
            ->leftJoin('st.assessmentOption', 'ao')
            ->where($qb->expr()->eq('s.published', 1))
            ->andWhere($qb->expr()->isNull('o.id'))
            ->andWhere($qb->expr()->eq('r.id', ':id'))
            ->groupBy('s.id')
            ->addGroupBy('s.title')
            ->addGroupBy('s.description')
            ->addGroupBy('am.id')
            ->addGroupBy('st.assessment')
            ->setParameter(':id', $report->getId());

        if (! empty($excludedSessionIds)) {
            $qb->andWhere($qb->expr()->notIn('s.id', ':excludedSessions'))
                ->setParameter(':excludedSessions', $excludedSessionIds);
        }

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    public function getEventsFromOfferingsOnlySessions(
        CurriculumInventoryReportInterface $report,
        array $excludedSessionIds = []
    ) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(
            's.id AS event_id, s.title, s.description, am.id AS method_id,'
            . 'st.assessment AS is_assessment_method, ao.name AS assessment_option_name, o.startDate, o.endDate'
        )
            ->from(Session::class, 's')
            ->join('s.course', 'c')
            ->join('c.sequenceBlocks', 'sb')
            ->join('sb.report', 'r')
            ->join('s.offerings', 'o')
            ->leftJoin('s.ilmSession', 'sf')
            ->leftJoin('s.sessionType', 'st')
            ->leftJoin('st.aamcMethods', 'am')
            ->leftJoin('st.assessmentOption', 'ao')
            ->where($qb->expr()->eq('s.published', 1))
            ->andWhere($qb->expr()->isNull('sf.id'))
            ->andWhere($qb->expr()->eq('r.id', ':id'))
            ->setParameter(':id', $report->getId());

        if (! empty($excludedSessionIds)) {
            $qb->andWhere($qb->expr()->notIn('s.id', ':excludedSessions'))
                ->setParameter(':excludedSessions', $excludedSessionIds);
        }

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    public function getEventsFromIlmSessionsWithOfferings(
        CurriculumInventoryReportInterface $report,
        array $excludedSessionIds = []
    ) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(
            's.id AS event_id, s.title, s.description, am.id AS method_id, sf.hours as ilm_hours,'
            . 'st.assessment AS is_assessment_method, ao.name AS assessment_option_name, o.startDate, o.endDate'
        )
            ->from(Session::class, 's')
            ->join('s.course', 'c')
            ->join('c.sequenceBlocks', 'sb')
            ->join('sb.report', 'r')
            ->join('s.offerings', 'o')
            ->join('s.ilmSession', 'sf')
            ->leftJoin('s.sessionType', 'st')
            ->leftJoin('st.aamcMethods', 'am')
            ->leftJoin('st.assessmentOption', 'ao')
            ->where($qb->expr()->eq('s.published', 1))
            ->andWhere($qb->expr()->eq('r.id', ':id'))
            ->setParameter(':id', $report->getId());

        if (! empty($excludedSessionIds)) {
            $qb->andWhere($qb->expr()->notIn('s.id', ':excludedSessions'))
                ->setParameter(':excludedSessions', $excludedSessionIds);
        }

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * Get all ids of sessions that are flagged to have their offerings counted as one in the given report.
     */
    public function getCountForOneOfferingSessionIds(CurriculumInventoryReportInterface $report): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('s.id')
            ->distinct()
            ->from(CurriculumInventoryReport::class, 'r')
            ->join('r.sequenceBlocks', 'sb')
            ->join('sb.sessions', 's')
            ->where($qb->expr()->eq('r.id', ':id'))
            ->setParameter(':id', $report->getId());
        $rows = $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);

        return array_column($rows, 'id');
    }

    /**
     * Get all ids of sessions that are flagged to be excluded from the given report.
     */
    public function getExcludedSessionIds(CurriculumInventoryReportInterface $report): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('s.id')
            ->distinct()
            ->from(CurriculumInventoryReport::class, 'r')
            ->join('r.sequenceBlocks', 'sb')
            ->join('sb.excludedSessions', 's')
            ->where($qb->expr()->eq('r.id', ':id'))
            ->setParameter(':id', $report->getId());
        $rows = $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);

        return array_column($rows, 'id');
    }
}
