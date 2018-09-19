<?php
namespace App\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\DTO\CurriculumInventoryReportDTO;
use App\Entity\CurriculumInventoryReportInterface;

/**
 * Class CurriculumInventoryReportRepository
 */
class CurriculumInventoryReportRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('App\Entity\CurriculumInventoryReport', 'x');

        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find and hydrate as DTOs
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return array
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder()->select('x')
            ->distinct()->from('App\Entity\CurriculumInventoryReport', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        /** @var CurriculumInventoryReportDTO[] $curriculumInventoryReportDTOs */
        $curriculumInventoryReportDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $curriculumInventoryReportDTOs[$arr['id']] = new CurriculumInventoryReportDTO(
                $arr['id'],
                $arr['name'],
                $arr['description'],
                $arr['year'],
                $arr['startDate'],
                $arr['endDate'],
                $arr['token']
            );
        }
        $curriculumInventoryReportIds = array_keys($curriculumInventoryReportDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, ' .
                'export.id AS exportId, sequence.id AS sequenceId, program.id AS programId, ' .
                'school.id AS schoolId'
            )
            ->from('App\Entity\CurriculumInventoryReport', 'x')
            ->join('x.program', 'program')
            ->join('program.school', 'school')
            ->leftJoin('x.sequence', 'sequence')
            ->leftJoin('x.export', 'export')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $curriculumInventoryReportIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $curriculumInventoryReportDTOs[$arr['xId']]->export = $arr['exportId']?(int)$arr['exportId']:null;
            $curriculumInventoryReportDTOs[$arr['xId']]->sequence = $arr['sequenceId']?(int)$arr['sequenceId']:null;
            $curriculumInventoryReportDTOs[$arr['xId']]->program = $arr['programId']?(int)$arr['programId']:null;
            $curriculumInventoryReportDTOs[$arr['xId']]->school = $arr['schoolId']?(int)$arr['schoolId']:null;
        }

        $related = [
            'sequenceBlocks',
            'academicLevels',
            'administrators',
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS curriculumInventoryReportId')
                ->from('App\Entity\CurriculumInventoryReport', 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':ids'))
                ->orderBy('relId')
                ->setParameter('ids', $curriculumInventoryReportIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $curriculumInventoryReportDTOs[$arr['curriculumInventoryReportId']]->{$rel}[] = $arr['relId'];
            }
        }
        return array_values($curriculumInventoryReportDTOs);
    }


    /**
     * @param QueryBuilder $qb
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return QueryBuilder
     */
    protected function attachCriteriaToQueryBuilder(QueryBuilder $qb, $criteria, $orderBy, $limit, $offset)
    {
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

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("x.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('x.'.$sort, $order);
            }
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb;
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @param array|int[] $eventIds
     * @return array
     */
    public function getEventResourceTypes(CurriculumInventoryReportInterface $report, array $eventIds = array())
    {
        if (empty($eventIds)) {
            return [];
        }

        $qb = $this->_em->createQueryBuilder();
        $qb->select('s.id AS event_id, art.id AS resource_type_id, art.title AS resource_type_title')
            ->distinct()
            ->from('App\Entity\Session', 's')
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
     * @param CurriculumInventoryReportInterface $report
     * @param array|int[] $eventIds
     * @return array
     */
    public function getEventKeywords(CurriculumInventoryReportInterface $report, array $eventIds = array())
    {
        $rhett = [];

        if (empty($eventIds)) {
            return $rhett;
        }

        $qb = $this->_em->createQueryBuilder();
        $qb->select("s.id AS event_id, md.id, 'MeSH' AS source, md.name")
            ->from('App\Entity\Session', 's')
            ->join('s.course', 'c')
            ->join('c.sequenceBlocks', 'sb')
            ->join('sb.report', 'r')
            ->join('s.meshDescriptors', 'md')
            ->where($qb->expr()->eq('r.id', ':id'))
            ->andWhere($qb->expr()->in('s.id', ':eventIds'))
            ->setParameter('id', $report->getId())
            ->setParameter('eventIds', $eventIds);

        $queries[] = $qb->getQuery();
        $qb = $this->_em->createQueryBuilder();
        $qb->select("s.id AS event_id, t.id, v.title AS source, t.title AS name")
            ->from('App\Entity\Session', 's')
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
            /* @var Query $query */
            $rhett = array_merge($rhett, $query->getResult(AbstractQuery::HYDRATE_ARRAY));
        }
        return $rhett;
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
        if (empty($eventIds)) {
            return [];
        }

        $qb = $this->_em->createQueryBuilder();
        $qb->select('sb.id, s.id AS event_id, s.supplemental AS optional')
            ->from('App\Entity\Session', 's')
            ->join('s.course', 'c')
            ->join('c.sequenceBlocks', 'sb')
            ->join('sb.report', 'r')
            ->where($qb->expr()->eq('r.id', ':id'))
            ->andWhere($qb->expr()->in('s.id', ':eventIds'))
            ->setParameter('id', $report->getId())
            ->setParameter('eventIds', $eventIds);

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @return array
     */
    public function getProgramObjectives(CurriculumInventoryReportInterface $report)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('o.id, o.title, a.id AS ancestor_id')
            ->distinct()
            ->from('App\Entity\CurriculumInventoryReport', 'r')
            ->join('r.program', 'p')
            ->join('p.school', 's')
            ->join('r.sequenceBlocks', 'sb')
            ->join('sb.course', 'c')
            ->join('c.cohorts', 'co')
            ->join('co.programYear', 'py')
            ->join('py.program', 'p2')
            ->join('p2.school', 's2')
            ->join('py.objectives', 'o')
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

    /**
     * @param CurriculumInventoryReportInterface $report
     * @return array
     */
    public function getCourseObjectives(CurriculumInventoryReportInterface $report)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('o.id, o.title')
            ->distinct()
            ->from('App\Entity\CurriculumInventoryReport', 'r')
            ->join('r.program', 'p')
            ->join('r.sequenceBlocks', 'sb')
            ->join('sb.course', 'c')
            ->join('c.objectives', 'o')
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
     * @param CurriculumInventoryReportInterface $report
     * @param array|int[] $sessionIds
     * @return array
     */
    public function getSessionObjectives(CurriculumInventoryReportInterface $report, array $sessionIds = array())
    {
        $rhett = [];

        if (empty($sessionIds)) {
            return $rhett;
        }

        $qb = $this->_em->createQueryBuilder();
        $qb->select('o.id, o.title')
            ->distinct()
            ->from('App\Entity\CurriculumInventoryReport', 'r')
            ->join('r.program', 'p')
            ->join('r.sequenceBlocks', 'sb')
            ->join('sb.course', 'c')
            ->join('c.sessions', 's')
            ->join('s.objectives', 'o')
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

    /**
     * @param CurriculumInventoryReportInterface $report
     * @param array $eventIds
     * @return array
     */
    public function getCompetencyObjectReferencesForEvents(
        CurriculumInventoryReportInterface $report,
        array $eventIds = array()
    ) {
        if (empty($eventIds)) {
            return [];
        }

        $qb = $this->_em->createQueryBuilder();
        $qb->select(
            's.id AS event_id, so.id AS session_objective_id, co.id AS course_objective_id,'
            . 'po.id AS program_objective_id'
        )
            ->distinct()
            ->from('App\Entity\CurriculumInventoryReport', 'r')
            ->join('r.program', 'p')
            ->join('r.sequenceBlocks', 'sb')
            ->join('sb.course', 'c')
            ->join('c.sessions', 's')
            ->leftJoin('s.objectives', 'so')
            ->leftJoin('so.parents', 'co')
            ->leftJoin('co.parents', 'po')
            ->where($qb->expr()->eq('r.id', ':id'))
            ->andWhere($qb->expr()->in('s.id', ':eventIds'))
            ->setParameter('id', $report->getId())
            ->setParameter('eventIds', $eventIds);

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @return array
     */
    public function getCompetencyObjectReferencesForSequenceBlocks(CurriculumInventoryReportInterface $report)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('sb.id, co.id AS course_objective_id, po.id AS program_objective_id')
            ->distinct()
            ->from('App\Entity\CurriculumInventoryReport', 'r')
            ->join('r.program', 'p')
            ->join('p.school', 's')
            ->join('r.sequenceBlocks', 'sb')
            ->join('sb.course', 'c')
            ->leftJoin('c.objectives', 'co')
            ->leftJoin('co.parents', 'po')
            ->where($qb->expr()->eq('r.id', ':id'))
            ->setParameter('id', $report->getId());

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * @param array $programObjectivesId
     * @param array $pcrsIds
     * @return array
     */
    public function getProgramObjectivesToPcrsRelations(array $programObjectivesId, array $pcrsIds)
    {
        if (! count($programObjectivesId) || ! count($pcrsIds)) {
            return [];
        }

        $qb = $this->_em->createQueryBuilder();
        $qb->select('o.id as objective_id, am.id AS pcrs_id')
            ->distinct()
            ->from('App\Entity\Objective', 'o')
            ->join('o.competency', 'c')
            ->join('c.aamcPcrses', 'am')
            ->where($qb->expr()->in('am.id', ':pcrs'))
            ->andWhere($qb->expr()->in('o.id', ':objectives'))
            ->setParameter(':pcrs', $pcrsIds)
            ->setParameter(':objectives', $programObjectivesId);

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * @param array $courseObjectiveIds
     * @param array $programObjectiveIds
     * @return array
     */
    public function getCourseObjectivesToProgramObjectivesRelations(
        array $courseObjectiveIds,
        array $programObjectiveIds
    ) {
        if (! count($courseObjectiveIds) || ! count($programObjectiveIds)) {
            return [];
        }

        $qb = $this->_em->createQueryBuilder();
        $qb->select('o.id AS objective_id, p.id AS parent_objective_id')
            ->distinct()
            ->from('App\Entity\Objective', 'o')
            ->join('o.courses', 'c')
            ->join('o.parents', 'p')
            ->where($qb->expr()->in('p.id', ':programObjectiveIds'))
            ->andWhere($qb->expr()->in('o.id', ':courseObjectiveIds'))
            ->setParameter(':courseObjectiveIds', $courseObjectiveIds)
            ->setParameter(':programObjectiveIds', $programObjectiveIds);

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
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
        if (! count($sessionObjectiveIds) || ! count($courseObjectiveIds)) {
            return [];
        }

        $qb = $this->_em->createQueryBuilder();
        $qb->select('o.id AS objective_id, p.id AS parent_objective_id')
            ->distinct()
            ->from('App\Entity\Objective', 'o')
            ->join('o.sessions', 's')
            ->join('o.parents', 'p')
            ->where($qb->expr()->in('p.id', ':courseObjectiveIds'))
            ->andWhere($qb->expr()->in('o.id', ':sessionObjectiveIds'))
            ->setParameter(':sessionObjectiveIds', $sessionObjectiveIds)
            ->setParameter(':courseObjectiveIds', $courseObjectiveIds);

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @return array
     */
    public function getPcrs(CurriculumInventoryReportInterface $report)
    {
        $rhett = [];
        $qb = $this->_em->createQueryBuilder();
        $qb->select('am.id AS pcrs_id, am.description')
            ->from('App\Entity\CurriculumInventoryReport', 'r')
            ->join('r.program', 'p')
            ->join('p.school', 's')
            ->join('r.sequenceBlocks', 'sb')
            ->join('sb.course', 'c')
            ->join('c.cohorts', 'co')
            ->join('co.programYear', 'py')
            ->join('py.objectives', 'o')
            ->join('o.competency', 'cm')
            ->join('cm.school', 's2')
            ->join('cm.parent', 'cm2')
            ->join('cm2.aamcPcrses', 'am')
            ->where($qb->expr()->eq('s.id', 's2.id'))
            ->andWhere($qb->expr()->eq('r.id', ':id'))
            ->setParameter(':id', $report->getId());
        $queries[] = $qb->getQuery();

        $qb = $this->_em->createQueryBuilder();
        $qb->select('am.id AS pcrs_id, am.description')
            ->from('App\Entity\CurriculumInventoryReport', 'r')
            ->join('r.program', 'p')
            ->join('p.school', 's')
            ->join('r.sequenceBlocks', 'sb')
            ->join('sb.course', 'c')
            ->join('c.cohorts', 'co')
            ->join('co.programYear', 'py')
            ->join('py.objectives', 'o')
            ->join('o.competency', 'cm')
            ->join('cm.school', 's2')
            ->join('cm.aamcPcrses', 'am')
            ->where($qb->expr()->eq('s.id', 's2.id'))
            ->andWhere($qb->expr()->eq('r.id', ':id'))
            ->setParameter(':id', $report->getId());
        $queries[] = $qb->getQuery();

        foreach ($queries as $query) {
            /* @var Query $query */
            $rows = $query->getResult(AbstractQuery::HYDRATE_ARRAY);
            foreach ($rows as $row) {
                $rhett[$row['pcrs_id']] = $row;
            }
        }
        return $rhett;
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @param array $excludedSessionIds
     * @return array
     */
    public function getEventsFromIlmOnlySessions(
        CurriculumInventoryReportInterface $report,
        array $excludedSessionIds = []
    ) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(
            's.id AS event_id, s.title, sd.description, am.id AS method_id,'
            . 'st.assessment AS is_assessment_method, ao.name AS assessment_option_name, sf.hours'
        )
            ->from('App\Entity\Session', 's')
            ->join('s.course', 'c')
            ->join('s.ilmSession', 'sf')
            ->join('c.sequenceBlocks', 'sb')
            ->join('sb.report', 'r')
            ->leftJoin('s.offerings', 'o')
            ->leftJoin('s.sessionDescription', 'sd')
            ->leftJoin('s.sessionType', 'st')
            ->leftJoin('st.aamcMethods', 'am')
            ->leftJoin('st.assessmentOption', 'ao')
            ->where($qb->expr()->eq('s.published', 1))
            ->andWhere($qb->expr()->isNull('o.id'))
            ->andWhere($qb->expr()->eq('r.id', ':id'))
            ->groupBy('s.id')
            ->addGroupBy('s.title')
            ->addGroupBy('sd.description')
            ->addGroupBy('am.id')
            ->addGroupBy('st.assessment')
            ->setParameter(':id', $report->getId());

        if (! empty($excludedSessionIds)) {
            $qb->andWhere($qb->expr()->notIn('s.id', ':excludedSessions'))
                ->setParameter(':excludedSessions', $excludedSessionIds);
        }

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @param array $excludedSessionIds
     * @return array
     */
    public function getEventsFromOfferingsOnlySessions(
        CurriculumInventoryReportInterface $report,
        array $excludedSessionIds = []
    ) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(
            's.id AS event_id, s.title, sd.description, am.id AS method_id,'
            . 'st.assessment AS is_assessment_method, ao.name AS assessment_option_name, o.startDate, o.endDate'
        )
            ->from('App\Entity\Session', 's')
            ->join('s.course', 'c')
            ->join('c.sequenceBlocks', 'sb')
            ->join('sb.report', 'r')
            ->join('s.offerings', 'o')
            ->leftJoin('s.ilmSession', 'sf')
            ->leftJoin('s.sessionDescription', 'sd')
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

    /**
     * @param CurriculumInventoryReportInterface $report
     * @param array $excludedSessionIds
     * @return array
     */
    public function getEventsFromIlmSessionsWithOfferings(
        CurriculumInventoryReportInterface $report,
        array $excludedSessionIds = []
    ) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(
            's.id AS event_id, s.title, sd.description, am.id AS method_id, sf.hours as ilm_hours,'
            . 'st.assessment AS is_assessment_method, ao.name AS assessment_option_name, o.startDate, o.endDate'
        )
            ->from('App\Entity\Session', 's')
            ->join('s.course', 'c')
            ->join('c.sequenceBlocks', 'sb')
            ->join('sb.report', 'r')
            ->join('s.offerings', 'o')
            ->join('s.ilmSession', 'sf')
            ->leftJoin('s.sessionDescription', 'sd')
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
     * @param CurriculumInventoryReportInterface $report
     * @return array|int[]
     */
    public function getCountForOneOfferingSessionIds(CurriculumInventoryReportInterface $report)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s.id')
            ->distinct()
            ->from('App\Entity\CurriculumInventoryReport', 'r')
            ->join('r.sequenceBlocks', 'sb')
            ->join('sb.sessions', 's')
            ->where($qb->expr()->eq('r.id', ':id'))
            ->setParameter(':id', $report->getId());
        $rows = $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);

        return array_column($rows, 'id');
    }

    /**
     * Get all ids of sessions that are flagged to be excluded from the given report.
     * @param CurriculumInventoryReportInterface $report
     * @return array|int[]
     */
    public function getExcludedSessionIds(CurriculumInventoryReportInterface $report)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s.id')
            ->distinct()
            ->from('App\Entity\CurriculumInventoryReport', 'r')
            ->join('r.sequenceBlocks', 'sb')
            ->join('sb.excludedSessions', 's')
            ->where($qb->expr()->eq('r.id', ':id'))
            ->setParameter(':id', $report->getId());
        $rows = $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);

        return array_column($rows, 'id');
    }
}
