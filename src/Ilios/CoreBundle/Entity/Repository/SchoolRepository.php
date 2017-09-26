<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Types\Type as DoctrineType;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Ilios\CoreBundle\Classes\SchoolEvent;
use Ilios\CoreBundle\Classes\UserEvent;
use Ilios\CoreBundle\Entity\DTO\SchoolDTO;

/**
 * Class SchoolRepository
 */
class SchoolRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * Custom findBy so we can filter by related entities
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT s')->from('IliosCoreBundle:School', 's');

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
        $qb = $this->_em->createQueryBuilder()->select('s')->distinct()->from('IliosCoreBundle:School', 's');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        $schoolDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $schoolDTOs[$arr['id']] = new SchoolDTO(
                $arr['id'],
                $arr['title'],
                $arr['templatePrefix'],
                $arr['iliosAdministratorEmail'],
                $arr['changeAlertRecipients']
            );
        }
        $schoolIds = array_keys($schoolDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select('s.id as schoolId, c.id as curriculumInventoryInstitutionId')
            ->from('IliosCoreBundle:School', 's')
            ->leftJoin('s.curriculumInventoryInstitution', 'c')
            ->where($qb->expr()->in('s.id', ':ids'))
            ->setParameter('ids', $schoolIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $schoolDTOs[$arr['schoolId']]->curriculumInventoryInstitution =
                $arr['curriculumInventoryInstitutionId'] ? $arr['curriculumInventoryInstitutionId'] : null;
        }

        $related = [
            'competencies',
            'courses',
            'programs',
            'departments',
            'vocabularies',
            'instructorGroups',
            'sessionTypes',
            'stewards',
            'directors',
            'administrators',
            'configurations'
        ];

        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, s.id AS schoolId')->from('IliosCoreBundle:School', 's')
                ->join("s.{$rel}", 'r')
                ->where($qb->expr()->in('s.id', ':schoolIds'))
                ->orderBy('relId')
                ->setParameter('schoolIds', $schoolIds);

            foreach ($qb->getQuery()->getResult() as $arr) {
                $schoolDTOs[$arr['schoolId']]->{$rel}[] = $arr['relId'];
            }
        }

        return array_values($schoolDTOs);
    }

    /**
     * Find all of the events for a user id between two dates.
     *
     * @param integer $id
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return UserEvent[]
     */
    public function findEventsForSchool($id, \DateTime $from, \DateTime $to)
    {
        $offeringEvents = [];
        $groupEvents = $this->getOfferingEventsFor($id, $from, $to);
        $offeringEvents = array_merge($offeringEvents, $groupEvents);


        $events = [];
        //extract unique offeringEvents by using the offering ID
        foreach ($offeringEvents as $userEvent) {
            if (!array_key_exists($userEvent->offering, $events)) {
                $events[$userEvent->offering] = $userEvent;
            }
        }

        $ilmEvents = [];
        $groupEvents = $this->getIlmSessionEventsFor($id, $from, $to);
        $ilmEvents = array_merge($ilmEvents, $groupEvents);

        $uniqueIlmEvents = [];
        //extract unique ilmEvents by using the ILM ID
        foreach ($ilmEvents as $userEvent) {
            if (!array_key_exists($userEvent->ilmSession, $uniqueIlmEvents)) {
                $uniqueIlmEvents[$userEvent->ilmSession] = $userEvent;
            }
        }

        $events = array_merge($events, $uniqueIlmEvents);
        //sort events by startDate and endDate for consistency
        usort($events, function ($a, $b) {
            $diff = $a->startDate->getTimestamp() - $b->startDate->getTimestamp();
            if ($diff === 0) {
                $diff = $a->endDate->getTimestamp() - $b->endDate->getTimestamp();
            }
            return $diff;
        });

        return $events;
    }

    /**
     * Use the query builder to get a set of offering based school events.
     *
     * @param integer $id
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return SchoolEvent[]
     */
    protected function getOfferingEventsFor(
        $id,
        \DateTime $from,
        \DateTime $to
    ) {
        $qb = $this->_em->createQueryBuilder();
        $what = 'c.id as courseId, s.id AS sessionId, ' .
          'o.id, o.startDate, o.endDate, o.room, o.updatedAt, o.updatedAt AS offeringUpdatedAt, ' .
          's.updatedAt AS sessionUpdatedAt, s.title, st.calendarColor, ' .
          's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
          's.attireRequired, s.equipmentRequired, s.supplemental, s.attendanceRequired, ' .
          'c.publishedAsTbd as coursePublishedAsTbd, c.published as coursePublished, c.title as courseTitle';
        $qb->add('select', $what)->from('IliosCoreBundle:School', 'school');
        $qb->join('school.courses', 'c');
        $qb->join('c.sessions', 's');
        $qb->join('s.offerings', 'o');
        $qb->leftJoin('s.sessionType', 'st');

        $qb->andWhere($qb->expr()->eq('school.id', ':school_id'));
        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->between('o.startDate', ':date_from', ':date_to'),
            $qb->expr()->andX(
                $qb->expr()->lte('o.startDate', ':date_from'),
                $qb->expr()->gte('o.endDate', ':date_from')
            )
        ));
        $qb->setParameter('school_id', $id);

        $qb->setParameter('date_from', $from, DoctrineType::DATETIME);
        $qb->setParameter('date_to', $to, DoctrineType::DATETIME);

        $results = $qb->getQuery()->getArrayResult();
        return $this->createEventObjectsForOfferings($id, $results);
    }

    /**
     * Use the query builder to get a set of ILMSession based user events.
     *
     * @param integer $id
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return SchoolEvent[]
     */
    protected function getIlmSessionEventsFor(
        $id,
        \DateTime $from,
        \DateTime $to
    ) {

        $qb = $this->_em->createQueryBuilder();

        $what = 'c.id as courseId, s.id AS sessionId, ' .
            'ilm.id, ilm.dueDate, ' .
            's.updatedAt, s.title, st.calendarColor, ' .
            's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
            's.attireRequired, s.equipmentRequired, s.supplemental, s.attendanceRequired, ' .
            'c.publishedAsTbd as coursePublishedAsTbd, c.published as coursePublished, c.title as courseTitle';
        $qb->add('select', $what)->from('IliosCoreBundle:School', 'school');

        $qb->join('school.courses', 'c');
        $qb->join('c.sessions', 's');
        $qb->join('s.ilmSession', 'ilm');
        $qb->leftJoin('s.sessionType', 'st');

        $qb->where($qb->expr()->andX(
            $qb->expr()->eq('school.id', ':school_id'),
            $qb->expr()->between('ilm.dueDate', ':date_from', ':date_to')
        ));
        $qb->setParameter('school_id', $id);

        $qb->setParameter('date_from', $from, DoctrineType::DATETIME);
        $qb->setParameter('date_to', $to, DoctrineType::DATETIME);

        $results = $qb->getQuery()->getArrayResult();
        return $this->createEventObjectsForIlmSessions($id, $results);
    }


    /**
     * Convert offerings into UserEvent objects.
     *
     * @param integer $schoolId
     * @param array $results
     *
     * @return SchoolEvent[]
     */
    protected function createEventObjectsForOfferings($schoolId, array $results)
    {
        return array_map(function ($arr) use ($schoolId) {
            $event = new SchoolEvent;
            $event->school = $schoolId;
            $event->name = $arr['title'];
            $event->startDate = $arr['startDate'];
            $event->endDate = $arr['endDate'];
            $event->offering = $arr['id'];
            $event->location = $arr['room'];
            $event->color = $arr['calendarColor'];
            $event->lastModified = max($arr['offeringUpdatedAt'], $arr['sessionUpdatedAt']);
            $event->isPublished = $arr['sessionPublished']  && $arr['coursePublished'];
            $event->isScheduled = $arr['sessionPublishedAsTbd'] || $arr['coursePublishedAsTbd'];
            $event->courseTitle = $arr['courseTitle'];
            $event->sessionId = $arr['sessionId'];
            $event->courseId = $arr['courseId'];
            $event->attireRequired = $arr['attireRequired'];
            $event->equipmentRequired = $arr['equipmentRequired'];
            $event->supplemental = $arr['supplemental'];
            $event->attendanceRequired = $arr['attendanceRequired'];
            return $event;
        }, $results);
    }


    /**
     * Convert IlmSessions into UserEvent objects
     * @param integer $schoolId
     * @param array $results
     *
     * @return SchoolEvent[]
     */
    protected function createEventObjectsForIlmSessions($schoolId, array $results)
    {
        return array_map(function ($arr) use ($schoolId) {
            $event = new SchoolEvent;
            $event->school = $schoolId;
            $event->name = $arr['title'];
            $event->startDate = $arr['dueDate'];
            $endDate = new \DateTime();
            $endDate->setTimestamp($event->startDate->getTimestamp());
            $event->endDate = $endDate->modify('+15 minutes');
            $event->ilmSession = $arr['id'];
            $event->color = $arr['calendarColor'];
            $event->lastModified = $arr['updatedAt'];
            $event->isPublished = $arr['sessionPublished']  && $arr['coursePublished'];
            $event->isScheduled = $arr['sessionPublishedAsTbd'] || $arr['coursePublishedAsTbd'];
            $event->courseTitle = $arr['courseTitle'];
            $event->sessionId = $arr['sessionId'];
            $event->courseId = $arr['courseId'];
            $event->attireRequired = $arr['attireRequired'];
            $event->equipmentRequired = $arr['equipmentRequired'];
            $event->supplemental = $arr['supplemental'];
            $event->attendanceRequired = $arr['attendanceRequired'];
            return $event;
        }, $results);
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
        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("s.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('s.'.$sort, $order);
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
}
