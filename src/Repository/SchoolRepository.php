<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\School;
use App\Entity\Session;
use App\Traits\ImportableEntityRepository;
use App\Traits\ManagerRepository;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Type as DoctrineType;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use App\Classes\CalendarEvent;
use App\Classes\SchoolEvent;
use App\Entity\DTO\SchoolDTO;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\UserMaterialFactory;
use App\Traits\CalendarEventRepository;

use function array_values;

class SchoolRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface,
    DataImportRepositoryInterface
{
    use CalendarEventRepository;
    use ManagerRepository;
    use ImportableEntityRepository;

    public function __construct(ManagerRegistry $registry, protected UserMaterialFactory $userMaterialFactory)
    {
        parent::__construct($registry, School::class);
    }

    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')->distinct()->from(School::class, 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new SchoolDTO(
                $arr['id'],
                $arr['title'],
                $arr['templatePrefix'],
                $arr['iliosAdministratorEmail'],
                $arr['changeAlertRecipients']
            );
        }
        return $this->attachAssociationsToDTOs($dtos);
    }

    /**
     * Find all of the events for a school by session
     * @return SchoolEvent[]
     */
    public function findSessionEventsForSchool(int $schoolId, int $sessionId): array
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
        return array_filter($events, fn(SchoolEvent $event) => $event->session === $sessionId);
    }

    /**
     * Find all of the events for a user id between two dates.
     *
     * @param int $id
     *
     * @return SchoolEvent[]
     */
    public function findEventsForSchool($id, DateTime $from, DateTime $to)
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

        $events = [...$events, ...$uniqueIlmEvents];

        //cast calendar events into school events
        $schoolEvents = array_map(fn(CalendarEvent $event) => SchoolEvent::createFromCalendarEvent($event), $events);

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
     * @param int $id
     *
     * @return CalendarEvent[]
     */
    protected function getOfferingEventsFor(
        $id,
        DateTime $from,
        DateTime $to
    ) {
        $qb = $this->_em->createQueryBuilder();
        $what = 'c.id as courseId, s.id AS sessionId, school.id AS schoolId, ' .
            'o.id, o.startDate, o.endDate, o.room, o.url, o.updatedAt, o.updatedAt AS offeringUpdatedAt, ' .
            's.updatedAt AS sessionUpdatedAt, s.title, st.calendarColor, st.title as sessionTypeTitle, ' .
            's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
            's.attireRequired, s.equipmentRequired, s.supplemental, s.attendanceRequired, s.instructionalNotes, ' .
            'c.publishedAsTbd as coursePublishedAsTbd, c.published as coursePublished, c.title as courseTitle, ' .
            'c.level as courseLevel, st.id as sessionTypeId, ' .
            'c.externalId as courseExternalId, s.description AS sessionDescription';

        $qb->addSelect($what)->from('App\Entity\School', 'school');
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
        return $this->createEventObjectsForOfferings($results);
    }

    /**
     * Use the query builder to get a set of ILMSession based user events.
     *
     * @param int $id
     *
     * @return CalendarEvent[]
     */
    protected function getIlmSessionEventsFor(
        $id,
        DateTime $from,
        DateTime $to
    ) {

        $qb = $this->_em->createQueryBuilder();

        $what = 'c.id as courseId, s.id AS sessionId, school.id AS schoolId, ' .
            'ilm.id, ilm.dueDate, ' .
            's.updatedAt, s.title, st.calendarColor, st.title as sessionTypeTitle, ' .
            's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
            's.attireRequired, s.equipmentRequired, s.supplemental, s.attendanceRequired, s.instructionalNotes, ' .
            'c.publishedAsTbd as coursePublishedAsTbd, c.published as coursePublished, c.title as courseTitle, ' .
            'c.level as courseLevel, st.id as sessionTypeId, ' .
            'c.externalId as courseExternalId, s.description AS sessionDescription';

        $qb->addSelect($what)->from('App\Entity\School', 'school');

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
        return $this->createEventObjectsForIlmSessions($results);
    }

    /**
     * Adds instructors to a given list of events.
     * @param SchoolEvent[] $events A list of events
     *
     * @return SchoolEvent[] The events list with instructors added.
     */
    public function addInstructorsToEvents(array $events)
    {
        return $this->attachInstructorsToEvents($events, $this->_em);
    }

    /**
     * Finds and adds learning materials to a given list of calendar events.
     *
     * @param SchoolEvent[] $events
     * @return SchoolEvent[]
     */
    public function addMaterialsToEvents(array $events)
    {
        return $this->attachMaterialsToEvents($events, $this->userMaterialFactory, $this->_em);
    }

    /**
     * Finds and adds course- and session-objectives and their competencies to a given list of calendar events.
     *
     * @param SchoolEvent[] $events
     * @return SchoolEvent[]
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
     * Get all the IDs
     *
     * @return int[]
     */
    public function getIds(): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->addSelect('x.id')->from(School::class, 'x');
        $results = $qb->getQuery()->getScalarResult();
        return array_map('intval', array_column($results, 'id'));
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
            'o.id, o.startDate, o.endDate, o.room, o.url, o.updatedAt, o.updatedAt AS offeringUpdatedAt, ' .
            's.updatedAt AS sessionUpdatedAt, s.title, st.calendarColor, st.title as sessionTypeTitle, ' .
            's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
            's.attireRequired, s.equipmentRequired, s.supplemental, s.attendanceRequired, s.instructionalNotes, ' .
            'c.publishedAsTbd as coursePublishedAsTbd, c.published as coursePublished, c.title as courseTitle, ' .
            'c.level as courseLevel, st.id as sessionTypeId, ' .
            'c.externalId as courseExternalId, s.description AS sessionDescription';

        $qb = $this->_em->createQueryBuilder();
        $qb->addSelect($what)->from('App\Entity\School', 'school');
        $qb->join('school.courses', 'c');
        $qb->join('c.sessions', 's');
        $qb->join('s.offerings', 'o');
        $qb->leftJoin('s.sessionType', 'st');
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
            'c.level as courseLevel, st.id as sessionTypeId, ' .
            'c.externalId as courseExternalId, s.description AS sessionDescription';

        $qb = $this->_em->createQueryBuilder();
        $qb->addSelect($what)->from('App\Entity\School', 'school');
        $qb->join('school.courses', 'c');
        $qb->join('c.sessions', 's');
        $qb->join('s.ilmSession', 'ilm');
        $qb->leftJoin('s.sessionType', 'st');
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
            $prerequisite = SchoolEvent::createFromCalendarEvent($this->createEventObjectForIlmSession($result));
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
            'o.id, o.startDate, o.endDate, o.room, o.url, o.updatedAt, o.updatedAt AS offeringUpdatedAt, ' .
            's.updatedAt AS sessionUpdatedAt, s.title, st.calendarColor, st.title as sessionTypeTitle, ' .
            's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
            's.attireRequired, s.equipmentRequired, s.supplemental, s.attendanceRequired, s.instructionalNotes, ' .
            'c.publishedAsTbd as coursePublishedAsTbd, c.published as coursePublished, c.title as courseTitle, ' .
            'c.level as courseLevel, st.id as sessionTypeId, ' .
            'c.externalId as courseExternalId, s.description AS sessionDescription';

        $qb = $this->_em->createQueryBuilder();
        $qb->addSelect($what)->from('App\Entity\School', 'school');
        $qb->join('school.courses', 'c');
        $qb->join('c.sessions', 's');
        $qb->join('s.offerings', 'o');
        $qb->leftJoin('s.sessionType', 'st');
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
            'c.level as courseLevel, st.id as sessionTypeId, ' .
            'c.externalId as courseExternalId, s.description AS sessionDescription';

        $qb = $this->_em->createQueryBuilder();
        $qb->addSelect($what)->from('App\Entity\School', 'school');
        $qb->join('school.courses', 'c');
        $qb->join('c.sessions', 's');
        $qb->join('s.ilmSession', 'ilm');
        $qb->leftJoin('s.sessionType', 'st');
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
                $this->createEventObjectForIlmSession($result)
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

    protected function attachCriteriaToQueryBuilder(
        QueryBuilder $qb,
        array $criteria,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): void {
        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }

    protected function attachAssociationsToDTOs(array $dtos): array
    {
        $schoolIds = array_keys($dtos);

        $qb = $this->_em->createQueryBuilder();
        $qb->select('s.id as schoolId, c.id as curriculumInventoryInstitutionId')
            ->from(School::class, 's')
            ->leftJoin('s.curriculumInventoryInstitution', 'c')
            ->where($qb->expr()->in('s.id', ':ids'))
            ->setParameter('ids', $schoolIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['schoolId']]->curriculumInventoryInstitution =
                $arr['curriculumInventoryInstitutionId'] ? $arr['curriculumInventoryInstitutionId'] : null;
        }

        $dtos = $this->attachRelatedToDtos(
            $dtos,
            [
                'competencies',
                'courses',
                'programs',
                'vocabularies',
                'instructorGroups',
                'sessionTypes',
                'directors',
                'administrators',
                'configurations',
            ],
        );

        return array_values($dtos);
    }

    public function import(array $data, string $type, array $referenceMap): array
    {
        // `school_id`,`template_prefix`,`title`,`ilios_administrator_email`,`change_alert_recipients`
        $entity = new School();
        $entity->setId($data[0]);
        $entity->setTemplatePrefix($data[1]);
        $entity->setTitle($data[2]);
        $entity->setIliosAdministratorEmail($data[3]);
        $entity->setChangeAlertRecipients($data[4]);
        $this->importEntity($entity);
        $referenceMap[$type . $entity->getId()] = $entity;
        return $referenceMap;
    }
}
