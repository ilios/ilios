<?php
namespace App\Entity\Repository;

use App\Entity\Session;
use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Types\Type as DoctrineType;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use App\Classes\CalendarEvent;
use App\Classes\SchoolEvent;
use App\Entity\DTO\SchoolDTO;
use App\Service\UserMaterialFactory;
use App\Traits\CalendarEventRepository;

/**
 * Class SchoolRepository
 */
class SchoolRepository extends EntityRepository implements DTORepositoryInterface
{
    use CalendarEventRepository;
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
        $qb->select('DISTINCT s')->from('App\Entity\School', 's');

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
        $qb = $this->_em->createQueryBuilder()->select('s')->distinct()->from('App\Entity\School', 's');
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
            ->from('App\Entity\School', 's')
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
                ->select('r.id AS relId, s.id AS schoolId')->from('App\Entity\School', 's')
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
     * Find all of the events for a school by session
     * @param integer $schoolId
     * @param integer $sessionId
     * @return SchoolEvent[]
     */
    public function findSessionEventsForSchool(int $schoolId, int $sessionId) : array
    {
        $qb = $this->_em->createQueryBuilder();
        $dates = $qb->select('o.startDate, o.endDate')
            ->from(Session::class, 's')
            ->leftJoin('s.offerings', 'o')
            ->where($qb->expr()->eq('s.id', ':session_id'))
            ->setParameter('session_id', $sessionId)
            ->getQuery()
            ->getArrayResult();
        $startDates = array_column($dates, 'startDate');
        $endDates = array_column($dates, 'endDate');
        sort($startDates);
        sort($endDates);
        $from = array_shift($startDates);
        $to = array_pop($endDates);
        $events = $this->findEventsForSchool($schoolId, $from, $to);
        return array_filter($events, function (SchoolEvent $event) use ($sessionId) {
            return $event->session === $sessionId;
        });
    }

    /**
     * Find all of the events for a user id between two dates.
     *
     * @param integer $id
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return SchoolEvent[]
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

        //cast calendar events into school events
        $schoolEvents = array_map(function (CalendarEvent $event) {
            return SchoolEvent::createFromCalendarEvent($event);
        }, $events);

        //sort events by startDate and endDate for consistency
        usort($schoolEvents, function ($a, $b) {
            $diff = $a->startDate->getTimestamp() - $b->startDate->getTimestamp();
            if ($diff === 0) {
                $diff = $a->endDate->getTimestamp() - $b->endDate->getTimestamp();
            }
            return $diff;
        });

        return $schoolEvents;
    }

    /**
     * Use the query builder to get a set of offering based school events.
     *
     * @param integer $id
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return CalendarEvent[]
     */
    protected function getOfferingEventsFor(
        $id,
        \DateTime $from,
        \DateTime $to
    ) {
        $qb = $this->_em->createQueryBuilder();
        $what = 'c.id as courseId, s.id AS sessionId, school.id AS schoolId, ' .
            'o.id, o.startDate, o.endDate, o.room, o.updatedAt, o.updatedAt AS offeringUpdatedAt, ' .
            's.updatedAt AS sessionUpdatedAt, s.title, st.calendarColor, st.title as sessionTypeTitle, ' .
            's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
            's.attireRequired, s.equipmentRequired, s.supplemental, s.attendanceRequired, s.instructionalNotes, ' .
            'c.publishedAsTbd as coursePublishedAsTbd, c.published as coursePublished, c.title as courseTitle, ' .
            'c.externalId as courseExternalId, sd.description AS sessionDescription';

        $qb->addSelect($what)->from('App\Entity\School', 'school');
        $qb->join('school.courses', 'c');
        $qb->join('c.sessions', 's');
        $qb->join('s.offerings', 'o');
        $qb->leftJoin('s.sessionType', 'st');
        $qb->leftJoin('s.sessionDescription', 'sd');

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
        return $this->createEventObjectsForOfferings($results);
    }

    /**
     * Use the query builder to get a set of ILMSession based user events.
     *
     * @param integer $id
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return CalendarEvent[]
     */
    protected function getIlmSessionEventsFor(
        $id,
        \DateTime $from,
        \DateTime $to
    ) {

        $qb = $this->_em->createQueryBuilder();

        $what = 'c.id as courseId, s.id AS sessionId, school.id AS schoolId, ' .
            'ilm.id, ilm.dueDate, ' .
            's.updatedAt, s.title, st.calendarColor, st.title as sessionTypeTitle, ' .
            's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
            's.attireRequired, s.equipmentRequired, s.supplemental, s.attendanceRequired, s.instructionalNotes, ' .
            'c.publishedAsTbd as coursePublishedAsTbd, c.published as coursePublished, c.title as courseTitle, ' .
            'c.externalId as courseExternalId, sd.description AS sessionDescription';

        $qb->addSelect($what)->from('App\Entity\School', 'school');

        $qb->join('school.courses', 'c');
        $qb->join('c.sessions', 's');
        $qb->join('s.ilmSession', 'ilm');
        $qb->leftJoin('s.sessionType', 'st');
        $qb->leftJoin('s.sessionDescription', 'sd');

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
     * Adds instructors to a given list of events.
     * @param CalendarEvent[] $events A list of events
     *
     * @return CalendarEvent[] The events list with instructors added.
     */
    public function addInstructorsToEvents(array $events)
    {
        return $this->attachInstructorsToEvents($events, $this->_em);
    }

    /**
     * Finds and adds learning materials to a given list of calendar events.
     *
     * @param CalendarEvent[] $events
     * @param UserMaterialFactory $factory
     * @return CalendarEvent[]
     */
    public function addMaterialsToEvents(array $events, UserMaterialFactory $factory)
    {
        return $this->attachMaterialsToEvents($events, $factory, $this->_em);
    }

    /**
     * Finds and adds course- and session-objectives and their competencies to a given list of calendar events.
     *
     * @param CalendarEvent[] $events
     * @return CalendarEvent[]
     */
    public function addSessionDataToEvents(array $events)
    {
        return $this->attachSessionDataToEvents($events, $this->_em);
    }

    /**
     * Adds pre- and post-requisites for a given school to a given list of events.
     * @param int $id The school id.
     * @param SchoolEvent[] $events A list of events.
     * @return SchoolEvent[] The events list with pre- and post-requisites added.
     */
    public function addPreAndPostRequisites($id, array $events)
    {
        $events = $this->attachPreRequisitesToEvents($id, $events);
        return $this->attachPostRequisitesToEvents($id, $events);
    }

    /**
     * Attaches school-events for a given user as pre-requisites to a given list of given events.
     * @param int $id The school id.
     * @param SchoolEvent[] $events A list of events.
     * @return array The events list with pre-requisites attached.
     */
    protected function attachPreRequisitesToEvents($id, array $events)
    {
        if (empty($events)) {
            return $events;
        }

        $sessionsMap = [];
        foreach ($events as $event) {
            $sessionId = $event->session;
            if (! array_key_exists($sessionId, $sessionsMap)) {
                $sessionsMap[$sessionId] = [];
            }
            $sessionsMap[$sessionId][] = $event;
        }
        $sessionIds = array_unique(array_column($events, 'session'));

        // get pre-requisites from offerings
        $what = 'ps.id AS preRequisiteSessionId, c.id as courseId, s.id AS sessionId, school.id AS schoolId, ' .
            'o.id, o.startDate, o.endDate, o.room, o.updatedAt, o.updatedAt AS offeringUpdatedAt, ' .
            's.updatedAt AS sessionUpdatedAt, s.title, st.calendarColor, st.title as sessionTypeTitle, ' .
            's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
            's.attireRequired, s.equipmentRequired, s.supplemental, s.attendanceRequired, s.instructionalNotes, ' .
            'c.publishedAsTbd as coursePublishedAsTbd, c.published as coursePublished, c.title as courseTitle, ' .
            'c.externalId as courseExternalId, sd.description AS sessionDescription';

        $qb = $this->_em->createQueryBuilder();
        $qb->addSelect($what)->from('App\Entity\School', 'school');
        $qb->join('school.courses', 'c');
        $qb->join('c.sessions', 's');
        $qb->join('s.offerings', 'o');
        $qb->leftJoin('s.sessionType', 'st');
        $qb->leftJoin('s.sessionDescription', 'sd');
        $qb->leftJoin('s.postrequisite', 'ps');
        $qb->where($qb->expr()->isNotNull('o.id'));
        $qb->andWhere($qb->expr()->in('ps.id', ':sessions'));
        $qb->andWhere($qb->expr()->eq('school.id', ':school_id'));
        $qb->setParameter('school_id', $id);
        $qb->setParameter('sessions', $sessionIds);

        $results = $qb->getQuery()->getArrayResult();

        // dedupe results by offering id
        $dedupedResults = [];
        foreach ($results as $result) {
            if (array_key_exists($result['id'], $dedupedResults)) {
                continue;
            }
            $dedupedResults[$result['id']] = $result;
        }
        $dedupedResults = array_values($dedupedResults);

        // create pre-requisites events and attach them to their proper events
        foreach ($dedupedResults as $result) {
            $prerequisite = SchoolEvent::createFromCalendarEvent($this->createEventObjectForOffering($result));
            $sessionId = $result['preRequisiteSessionId'];
            if (array_key_exists($sessionId, $sessionsMap)) {
                /** @var CalendarEvent $event */
                foreach ($sessionsMap[$sessionId] as $event) {
                    $event->prerequisites[] = $prerequisite;
                }
            }
        }

        // get pre-requisites from ILMs
        $what = 'ps.id AS preRequisiteSessionId, c.id as courseId, s.id AS sessionId, school.id as schoolId,' .
            'ilm.id, ilm.dueDate, ' .
            's.updatedAt, s.title, st.calendarColor, st.title as sessionTypeTitle, ' .
            's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
            's.attireRequired, s.equipmentRequired, s.supplemental, s.attendanceRequired, s.instructionalNotes, ' .
            'c.publishedAsTbd as coursePublishedAsTbd, c.published as coursePublished, c.title as courseTitle, ' .
            'c.externalId as courseExternalId, sd.description AS sessionDescription';

        $qb = $this->_em->createQueryBuilder();
        $qb->addSelect($what)->from('App\Entity\School', 'school');
        $qb->join('school.courses', 'c');
        $qb->join('c.sessions', 's');
        $qb->join('s.ilmSession', 'ilm');
        $qb->leftJoin('s.sessionType', 'st');
        $qb->leftJoin('s.sessionDescription', 'sd');
        $qb->leftJoin('s.postrequisite', 'ps');
        $qb->where($qb->expr()->isNotNull('ilm.id'));
        $qb->andWhere($qb->expr()->in('ps.id', ':sessions'));
        $qb->andWhere($qb->expr()->eq('school.id', ':school_id'));
        $qb->setParameter('school_id', $id);
        $qb->setParameter('sessions', $sessionIds);

        $results = $qb->getQuery()->getArrayResult();

        // dedupe results by ILM id
        $dedupedResults = [];
        foreach ($results as $result) {
            if (array_key_exists($result['id'], $dedupedResults)) {
                continue;
            }
            $dedupedResults[$result['id']] = $result;
        }
        $dedupedResults = array_values($dedupedResults);

        // create pre-requisites events and attach them to their proper events
        foreach ($dedupedResults as $result) {
            $prerequisite = SchoolEvent::createFromCalendarEvent($this->createEventObjectForIlmSession($id, $result));
            $sessionId = $result['preRequisiteSessionId'];
            if (array_key_exists($sessionId, $sessionsMap)) {
                /** @var CalendarEvent $event */
                foreach ($sessionsMap[$sessionId] as $event) {
                    $event->prerequisites[] = $prerequisite;
                }
            }
        }

        return $events;
    }

    /**
     * Attaches school-events for a given user as post-requisites to a given list of given events.
     * @param int $id The school id.
     * @param array $events A list of events.
     * @return array The events list with post-requisites attached.
     */
    protected function attachPostRequisitesToEvents($id, array $events)
    {
        if (empty($events)) {
            return $events;
        }

        $sessionsMap = [];
        foreach ($events as $event) {
            $sessionId = $event->session;
            if (! array_key_exists($sessionId, $sessionsMap)) {
                $sessionsMap[$sessionId] = [];
            }
            $sessionsMap[$sessionId][] = $event;
        }
        $sessionIds = array_unique(array_column($events, 'session'));

        // get post-requisites from offerings
        $what = 'ps.id AS postRequisiteSessionId, c.id as courseId, s.id AS sessionId, school.id AS schoolId, ' .
            'o.id, o.startDate, o.endDate, o.room, o.updatedAt, o.updatedAt AS offeringUpdatedAt, ' .
            's.updatedAt AS sessionUpdatedAt, s.title, st.calendarColor, st.title as sessionTypeTitle, ' .
            's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
            's.attireRequired, s.equipmentRequired, s.supplemental, s.attendanceRequired, s.instructionalNotes, ' .
            'c.publishedAsTbd as coursePublishedAsTbd, c.published as coursePublished, c.title as courseTitle, ' .
            'c.externalId as courseExternalId, sd.description AS sessionDescription';

        $qb = $this->_em->createQueryBuilder();
        $qb->addSelect($what)->from('App\Entity\School', 'school');
        $qb->join('school.courses', 'c');
        $qb->join('c.sessions', 's');
        $qb->join('s.offerings', 'o');
        $qb->leftJoin('s.sessionType', 'st');
        $qb->leftJoin('s.sessionDescription', 'sd');
        $qb->leftJoin('s.prerequisites', 'ps');
        $qb->where($qb->expr()->isNotNull('o.id'));
        $qb->andWhere($qb->expr()->in('ps.id', ':sessions'));
        $qb->andWhere($qb->expr()->eq('school.id', ':school_id'));
        $qb->setParameter('sessions', $sessionIds);
        $qb->setParameter('school_id', $id);

        $results = $qb->getQuery()->getArrayResult();

        // dedupe results by offering id
        $dedupedResults = [];
        foreach ($results as $result) {
            if (array_key_exists($result['postRequisiteSessionId'], $dedupedResults)) {
                continue;
            }
            $dedupedResults[$result['postRequisiteSessionId']] = $result;
        }
        $dedupedResults = array_values($dedupedResults);

        // create post-requisites events and attach them to their proper events
        foreach ($dedupedResults as $result) {
            $postrequisite = SchoolEvent::createFromCalendarEvent($this->createEventObjectForOffering($result));
            $sessionId = $result['postRequisiteSessionId'];
            if (array_key_exists($sessionId, $sessionsMap)) {
                /** @var CalendarEvent $event */
                foreach ($sessionsMap[$sessionId] as $event) {
                    $event->postrequisites[] = $postrequisite;
                }
            }
        }

        // get post-requisites from ILMs
        $what = 'ps.id AS postRequisiteSessionId, c.id as courseId, s.id AS sessionId, school.id AS schoolId, ' .
            'ilm.id, ilm.dueDate, ' .
            's.updatedAt, s.title, st.calendarColor, st.title as sessionTypeTitle, ' .
            's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
            's.attireRequired, s.equipmentRequired, s.supplemental, s.attendanceRequired, s.instructionalNotes, ' .
            'c.publishedAsTbd as coursePublishedAsTbd, c.published as coursePublished, c.title as courseTitle, ' .
            'c.externalId as courseExternalId, sd.description AS sessionDescription';

        $qb = $this->_em->createQueryBuilder();
        $qb->addSelect($what)->from('App\Entity\School', 'school');
        $qb->join('school.courses', 'c');
        $qb->join('c.sessions', 's');
        $qb->join('s.ilmSession', 'ilm');
        $qb->leftJoin('s.sessionType', 'st');
        $qb->leftJoin('s.sessionDescription', 'sd');
        $qb->leftJoin('s.prerequisites', 'ps');
        $qb->where($qb->expr()->isNotNull('ilm.id'));
        $qb->andWhere($qb->expr()->in('ps.id', ':sessions'));
        $qb->andWhere($qb->expr()->eq('school.id', ':school_id'));
        $qb->setParameter('school_id', $id);
        $qb->setParameter('sessions', $sessionIds);

        $results = $qb->getQuery()->getArrayResult();

        // dedupe results by ILM id
        $dedupedResults = [];
        foreach ($results as $result) {
            if (array_key_exists($result['postRequisiteSessionId'], $dedupedResults)) {
                continue;
            }
            $dedupedResults[$result['postRequisiteSessionId']] = $result;
        }
        $dedupedResults = array_values($dedupedResults);

        // create post-requisites events and attach them to their proper events
        foreach ($dedupedResults as $result) {
            $postrequisite = SchoolEvent::createFromCalendarEvent(
                $this->createEventObjectForIlmSession(null, $result)
            );
            $sessionId = $result['postRequisiteSessionId'];
            if (array_key_exists($sessionId, $sessionsMap)) {
                /** @var CalendarEvent $event */
                foreach ($sessionsMap[$sessionId] as $event) {
                    $event->postrequisites[] = $postrequisite;
                }
            }
        }

        return $events;
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
