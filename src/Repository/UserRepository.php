<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Offering;
use App\Entity\IlmSession;
use App\Classes\CalendarEventUserContext;
use App\Entity\Session;
use App\Entity\UserRole;
use App\Entity\UserRoleInterface;
use App\Service\DTOCacheManager;
use App\Traits\ManagerRepository;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\DBAL\Types\Types as DoctrineType;
use App\Classes\CalendarEvent;
use App\Classes\UserEvent;
use App\Classes\UserMaterial;
use App\Entity\User;
use App\Entity\DTO\UserDTO;
use App\Service\UserMaterialFactory;
use App\Traits\CalendarEventRepository;
use Doctrine\Persistence\ManagerRegistry;

use function array_keys;
use function array_values;

class UserRepository extends ServiceEntityRepository implements DTORepositoryInterface, RepositoryInterface
{
    use CalendarEventRepository;
    use ManagerRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected UserMaterialFactory $factory,
        protected DTOCacheManager $cacheManager,
    ) {
        parent::__construct($registry, User::class);
    }

    /**
     * Find by a string query
     */
    public function findDTOsByQ(
        string $q,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
        array $criteria = []
    ): array {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->addSelect('x')->from(User::class, 'x');
        $qb->leftJoin('x.authentication', 'auth');

        $terms = explode(' ', $q);
        $terms = array_filter($terms, 'strlen');
        if (empty($terms)) {
            return [];
        }

        foreach ($terms as $key => $term) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('x.firstName', "?{$key}"),
                $qb->expr()->like('x.lastName', "?{$key}"),
                $qb->expr()->like('x.middleName', "?{$key}"),
                $qb->expr()->like('x.displayName', "?{$key}"),
                $qb->expr()->like('x.email', "?{$key}"),
                $qb->expr()->like('x.preferredEmail', "?{$key}"),
                $qb->expr()->like('x.campusId', "?{$key}"),
                $qb->expr()->like('auth.username', "?{$key}")
            ))
                ->setParameter($key, '%' . $term . '%');
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('x.' . $sort, $order);
            }
        }
        if (array_key_exists('roles', $criteria)) {
            $roleIds = is_array($criteria['roles']) ? $criteria['roles'] : [$criteria['roles']];
            $qb->join('x.roles', 'r');
            $qb->andWhere($qb->expr()->in('r.id', ':roles'));
            $qb->setParameter(':roles', $roleIds);
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $this->createUserDTOs($qb->getQuery());
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()->select('x')->distinct()->from(User::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);

        return $this->createUserDTOs($qb->getQuery());
    }

    /**
     * Find and hydrate as DTOs
     *
     */
    public function findAllMatchingDTOsByCampusIds(array $campusIds): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('u')
            ->from(User::class, 'u')
            ->where($qb->expr()->in('u.campusId', ':campusIds'));
        $qb->setParameter(':campusIds', $campusIds);

        return $this->createUserDTOs($qb->getQuery());
    }

    /**
     * Find all of the events for a user id between two dates
     */
    public function findEventsForUser(int $id, DateTime $from, DateTime $to): array
    {
        //These joins are DQL representations to go from a user to an offerings
        $joinsAndUserContexts = $this->getUserToOfferingJoinsAndUserContexts();

        $offeringEvents = [];
        //using each of the joins above create a query to get events
        foreach ($joinsAndUserContexts as $joinsAndUserContext) {
            $groupEvents = $this->getOfferingEventsFor(
                $id,
                $from,
                $to,
                $joinsAndUserContext[0],
                $joinsAndUserContext[1],
            );
            $offeringEvents = array_merge($offeringEvents, $groupEvents);
        }

        $events = [];
        //extract unique offeringEvents by using the offering ID
        foreach ($offeringEvents as $userEvent) {
            if (!array_key_exists($userEvent->offering, $events)) {
                $events[$userEvent->offering] = $userEvent;
            } else {
                $events[$userEvent->offering]->userContexts = array_unique(
                    array_merge(
                        $events[$userEvent->offering]->userContexts,
                        $userEvent->userContexts
                    )
                );
            }
        }

        //These joins are DQL representations to go from a user to an ILMSession
        $joinsAndUserContexts = $this->getUserToIlmJoinsAndUserContexts();

        $ilmEvents = [];
        //using each of the joins above create a query to get events
        foreach ($joinsAndUserContexts as $joinsAndUserContext) {
            $groupEvents = $this->getIlmSessionEventsFor(
                $id,
                $from,
                $to,
                $joinsAndUserContext[0],
                $joinsAndUserContext[1]
            );
            $ilmEvents = array_merge($ilmEvents, $groupEvents);
        }

        $uniqueIlmEvents = [];
        //extract unique ilmEvents by using the ILM ID
        foreach ($ilmEvents as $userEvent) {
            if (!array_key_exists($userEvent->ilmSession, $uniqueIlmEvents)) {
                $uniqueIlmEvents[$userEvent->ilmSession] = $userEvent;
            } else {
                $uniqueIlmEvents[$userEvent->ilmSession]->userContexts = array_unique(
                    array_merge(
                        $uniqueIlmEvents[$userEvent->ilmSession]->userContexts,
                        $userEvent->userContexts
                    )
                );
            }
        }

        $events = [...$events, ...$uniqueIlmEvents];

        //turn calendar events into user events
        $userEvents = array_map(fn(CalendarEvent $event) => UserEvent::createFromCalendarEvent($id, $event), $events);

        //sort events by startDate and endDate for consistency
        usort($userEvents, function ($a, $b) {
            $diff = $a->startDate->getTimestamp() - $b->startDate->getTimestamp();
            if ($diff === 0) {
                $diff = $a->endDate->getTimestamp() - $b->endDate->getTimestamp();
            }
            return $diff;
        });

        return $userEvents;
    }

    /**
     * Find all of the events for a user in a session
     */
    public function findSessionEventsForUser(int $userId, int $sessionId): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
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
        $events = $this->findEventsForUser($userId, $from, $to);
        return array_filter($events, fn(UserEvent $event) => $event->session === $sessionId);
    }

    /**
     * Get a list of users who do not have the former student role filtered by campus id
     */
    public function findUsersWhoAreNotFormerStudents(array $campusIds = []): Collection
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $formerStudentRole = $qb->select('r')
            ->from(UserRole::class, 'r')
            ->where($qb->expr()->eq('r.title', ':fs_role_title'))
            ->setParameter('fs_role_title', 'Former Student')
            ->getQuery()
            ->getSingleResult();

        return new ArrayCollection($this->findUsersWithoutRole($formerStudentRole, $campusIds));
    }

    /**
     * Get a list of users who do not have the student role filtered by campus id
     */
    public function findUsersWhoAreNotStudents(array $campusIds): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $studentRole = $qb->select('r')
            ->from(UserRole::class, 'r')
            ->where($qb->expr()->eq('r.title', ':fs_role_title'))
            ->setParameter('fs_role_title', 'Student')
            ->getQuery()
            ->getSingleResult();

        return $this->findUsersWithoutRole($studentRole, $campusIds);
    }

    /**
     * Find users who do not have a specific role
     * Optionally filter those users by campusId
     */
    public function findUsersWithoutRole(UserRoleInterface $role, array $campusIds = []): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $usersWithRole = $qb->select('u.id')
            ->from(UserRole::class, 'r')
            ->leftJoin('r.users', 'u')
            ->where($qb->expr()->eq('r.id', ':role_id'))
            ->setParameter('role_id', $role->getId())
            ->getQuery()
            ->getScalarResult();
        $userIds = array_map(fn(array $arr) => $arr['id'], $usersWithRole);

        $qb2 = $this->getEntityManager()->createQueryBuilder();
        $qb2->addSelect('u')
            ->from(User::class, 'u')
            ->andWhere($qb->expr()->notIn('u.id', $userIds))
            ->addOrderBy('u.lastName', 'ASC')
            ->addOrderBy('u.firstName', 'ASC');
        if ($campusIds !== []) {
            $qb2->andWhere($qb->expr()->in('u.campusId', $campusIds));
        }

        return $qb2->getQuery()->getResult();
    }

    /**
     * Get all the IDs for all users
     *
     */
    public function getIds(bool $includeDisabled = true, bool $includeSyncIgnore = true): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->addSelect('u.id')->from(User::class, 'u');
        if (!$includeDisabled) {
            $qb->andWhere('u.enabled=1');
        }
        if (!$includeSyncIgnore) {
            $qb->andWhere('u.userSyncIgnore=0');
        }

        return array_map(fn(array $arr) => $arr['id'], $qb->getQuery()->getScalarResult());
    }

    /**
     * Get all the campus IDs for all users
     *
     */
    public function getAllCampusIds(bool $includeDisabled = true, bool $includeSyncIgnore = true): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->addSelect('u.campusId')->from(User::class, 'u');
        if (!$includeDisabled) {
            $qb->andWhere('u.enabled=1');
        }
        if (!$includeSyncIgnore) {
            $qb->andWhere('u.userSyncIgnore=0');
        }

        return array_map(fn(array $arr) => $arr['campusId'], $qb->getQuery()->getScalarResult());
    }

    /**
     * Reset examined flag for all users
     */
    public function resetExaminedFlagForAllUsers(): void
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->update(User::class, 'u')
            ->set('u.examined', $qb->expr()->literal(false));

        $qb->getQuery()->execute();
    }

    /**
     * Use the query builder and the $joins to get a set of
     * offering based user events
     *
     */
    protected function getOfferingEventsFor(
        int $id,
        DateTime $from,
        DateTime $to,
        array $joins,
        string $userContext,
    ): array {

        $qb = $this->getEntityManager()->createQueryBuilder();
        $what = 'c.id as courseId, s.id AS sessionId, school.id AS schoolId, o.id, o.startDate, o.endDate, o.room, ' .
            'o.url, o.updatedAt AS offeringUpdatedAt, s.updatedAt AS sessionUpdatedAt, s.title, st.calendarColor, ' .
            's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
            's.attireRequired, s.equipmentRequired, s.supplemental, s.attendanceRequired, s.instructionalNotes, ' .
            'c.publishedAsTbd as coursePublishedAsTbd, c.published as coursePublished, c.title AS courseTitle, ' .
            'c.level as courseLevel, st.id as sessionTypeId, ' .
            's.description AS sessionDescription, st.title AS sessionTypeTitle, c.externalId AS courseExternalId';

        $qb->addSelect($what)->from(User::class, 'u');
        foreach ($joins as $key => $statement) {
            $qb->leftJoin($statement, $key);
        }
        $qb->leftJoin('o.session', 's');
        $qb->leftJoin('s.course', 'c');
        $qb->leftJoin('c.school', 'school');
        $qb->leftJoin('s.sessionType', 'st');

        $qb->andWhere($qb->expr()->eq('u.id', ':user_id'));
        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->between('o.startDate', ':date_from', ':date_to'),
            $qb->expr()->andX(
                $qb->expr()->lte('o.startDate', ':date_from'),
                $qb->expr()->gte('o.endDate', ':date_from')
            )
        ));
        $qb->setParameter('user_id', $id);

        $qb->setParameter('date_from', $from, DoctrineType::DATETIME_MUTABLE);
        $qb->setParameter('date_to', $to, DoctrineType::DATETIME_MUTABLE);

        $results = $qb->getQuery()->getArrayResult();
        return $this->createEventObjectsForOfferings($results, [$userContext]);
    }

    /**
     * Use the query builder and the $joins to get a set of
     * ILMSession based user events
     */
    protected function getIlmSessionEventsFor(
        int $id,
        DateTime $from,
        DateTime $to,
        array $joins,
        ?string $userContext = null
    ): array {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $what = 'c.id as courseId, s.id AS sessionId, school.id AS schoolId, ilm.id, ilm.dueDate, ' .
            's.updatedAt, s.title, st.calendarColor, ' .
            's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
            's.attireRequired, s.equipmentRequired, s.supplemental, s.attendanceRequired, s.instructionalNotes, ' .
            'c.publishedAsTbd as coursePublishedAsTbd, c.published as coursePublished, c.title as courseTitle,' .
            'c.level as courseLevel, st.id as sessionTypeId, ' .
            's.description AS sessionDescription, st.title AS sessionTypeTitle, c.externalId AS courseExternalId';

        $qb->addSelect($what)->from(User::class, 'u');

        foreach ($joins as $key => $statement) {
            $qb->leftJoin($statement, $key);
        }
        $qb->leftJoin('ilm.session', 's');
        $qb->leftJoin('s.course', 'c');
        $qb->leftJoin('c.school', 'school');
        $qb->leftJoin('s.sessionType', 'st');
        $qb->where($qb->expr()->andX(
            $qb->expr()->eq('u.id', ':user_id'),
            $qb->expr()->between('ilm.dueDate', ':date_from', ':date_to')
        ));
        $qb->setParameter('user_id', $id);
        $qb->setParameter('date_from', $from, DoctrineType::DATETIME_MUTABLE);
        $qb->setParameter('date_to', $to, DoctrineType::DATETIME_MUTABLE);

        $results = $qb->getQuery()->getArrayResult();

        return array_map(fn(CalendarEvent $event) => UserEvent::createFromCalendarEvent(
            $id,
            $event
        ), $this->createEventObjectsForIlmSessions($results, [$userContext]));
    }

    /**
     * Adds instructors to a given list of events.
     * @param UserEvent[] $events A list of events
     */
    public function addInstructorsToEvents(array $events): array
    {
        return $this->attachInstructorsToEvents($events, $this->getEntityManager());
    }

    /**
     * Adds pre- and post-requisites for a given user to a given list of events.
     * @param int $id The user id.
     * @param UserEvent[] $events A list of events.
     */
    public function addPreAndPostRequisites(int $id, array $events): array
    {
        $events = $this->attachPreRequisitesToEvents($id, $events);
        return $this->attachPostRequisitesToEvents($id, $events);
    }

    /**
     * Attaches user-events for a given user as pre-requisites to a given list of given events.
     * @param int $id The user id.
     * @param UserEvent[] $events A list of events.
     */
    protected function attachPreRequisitesToEvents(int $id, array $events): array
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

        // get pre-requisites from offerings that a the user is associated with (as learner, instructor, etc.)
        $results = [];
        $joinsAndUserContexts = $this->getUserToOfferingJoinsAndUserContexts();
        $what = 'ps.id AS preRequisiteSessionId, c.id as courseId, s.id AS sessionId, school.id AS schoolId, ' .
            'o.id, o.startDate, o.endDate, o.room, o.url, o.updatedAt, o.updatedAt AS offeringUpdatedAt, ' .
            's.updatedAt AS sessionUpdatedAt, s.title, st.calendarColor, st.title as sessionTypeTitle, ' .
            's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
            's.attireRequired, s.equipmentRequired, s.supplemental, s.attendanceRequired, s.instructionalNotes, ' .
            'c.publishedAsTbd as coursePublishedAsTbd, c.published as coursePublished, c.title as courseTitle, ' .
            'c.level as courseLevel, st.id as sessionTypeId, ' .
            'c.externalId as courseExternalId, s.description AS sessionDescription';
        foreach ($joinsAndUserContexts as $joinsAndUserContext) {
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->addSelect($what)->from(User::class, 'u');
            foreach ($joinsAndUserContext[0] as $key => $statement) {
                $qb->leftJoin($statement, $key);
            }
            $qb->leftJoin('o.session', 's');
            $qb->leftJoin('s.course', 'c');
            $qb->leftJoin('c.school', 'school');
            $qb->leftJoin('s.sessionType', 'st');
            $qb->leftJoin('s.postrequisite', 'ps');
            $qb->where($qb->expr()->isNotNull('o.id'));
            $qb->andWhere($qb->expr()->in('ps.id', ':sessions'));
            $qb->andWhere($qb->expr()->eq('u.id', ':user_id'));
            $qb->setParameter('user_id', $id);
            $qb->setParameter('sessions', $sessionIds);

            $result = $qb->getQuery()->getArrayResult();
            if (!empty($result)) {
                $result = array_map(function ($event) use ($joinsAndUserContext) {
                    $event['userContexts'] = [$joinsAndUserContext[1]];
                    return $event;
                }, $result);
                $results = array_merge($results, $result);
            }
        }

        // dedupe results by offering id
        $dedupedResults = [];
        foreach ($results as $result) {
            if (array_key_exists($result['id'], $dedupedResults)) {
                $dedupedResults[$result['id']]['userContexts'] = array_unique(
                    array_merge(
                        $dedupedResults[$result['id']]['userContexts'],
                        $result['userContexts']
                    )
                );
            }
            $dedupedResults[$result['id']] = $result;
        }
        $dedupedResults = array_values($dedupedResults);

        // create pre-requisites events and attach them to their proper events
        foreach ($dedupedResults as $result) {
            $prerequisite = UserEvent::createFromCalendarEvent(
                $id,
                $this->createEventObjectForOffering($result, $result['userContexts'])
            );
            $sessionId = $result['preRequisiteSessionId'];
            if (array_key_exists($sessionId, $sessionsMap)) {
                /** @var CalendarEvent $event */
                foreach ($sessionsMap[$sessionId] as $event) {
                    $event->prerequisites[] = $prerequisite;
                }
            }
        }

        // get pre-requisites from ILMs that a the user is associated with (as learner, instructor, etc.)
        $results = [];
        $joinsAndUserContexts = $this->getUserToIlmJoinsAndUserContexts();
        $what = 'ps.id AS preRequisiteSessionId, c.id as courseId, s.id AS sessionId, school.id AS schoolId, ' .
            'ilm.id, ilm.dueDate, s.updatedAt, s.title, st.calendarColor, ' .
            's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
            's.attireRequired, s.equipmentRequired, s.supplemental, s.attendanceRequired, s.instructionalNotes, ' .
            'c.publishedAsTbd as coursePublishedAsTbd, c.published as coursePublished, c.title as courseTitle,' .
            'c.level as courseLevel, st.id as sessionTypeId, ' .
            's.description AS sessionDescription, st.title AS sessionTypeTitle, c.externalId AS courseExternalId';
        foreach ($joinsAndUserContexts as $joinsAndUserContext) {
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->addSelect($what)->from(User::class, 'u');
            foreach ($joinsAndUserContext[0] as $key => $statement) {
                $qb->leftJoin($statement, $key);
            }
            $qb->leftJoin('ilm.session', 's');
            $qb->leftJoin('s.course', 'c');
            $qb->leftJoin('c.school', 'school');
            $qb->leftJoin('s.sessionType', 'st');
            $qb->leftJoin('s.postrequisite', 'ps');
            $qb->where($qb->expr()->isNotNull('ilm.id'));
            $qb->andWhere($qb->expr()->in('ps.id', ':sessions'));
            $qb->andWhere($qb->expr()->eq('u.id', ':user_id'));
            $qb->setParameter('user_id', $id);
            $qb->setParameter('sessions', $sessionIds);

            $result = $qb->getQuery()->getArrayResult();
            if (!empty($result)) {
                $result = array_map(function ($event) use ($joinsAndUserContext) {
                    $event['userContexts'] = [$joinsAndUserContext[1]];
                    return $event;
                }, $result);
                $results = array_merge($results, $result);
            }
        }

        // dedupe results by ILM id
        $dedupedResults = [];
        foreach ($results as $result) {
            if (array_key_exists($result['id'], $dedupedResults)) {
                $dedupedResults[$result['id']]['userContexts'] = array_unique(
                    array_merge(
                        $dedupedResults[$result['id']]['userContexts'],
                        $result['userContexts']
                    )
                );
            }
            $dedupedResults[$result['id']] = $result;
        }
        $dedupedResults = array_values($dedupedResults);

        // create pre-requisites events and attach them to their proper events
        foreach ($dedupedResults as $result) {
            $prerequisite = UserEvent::createFromCalendarEvent(
                $id,
                $this->createEventObjectForIlmSession($result, $result['userContexts'])
            );
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
     * Attaches user-events for a given user as post-requisites to a given list of given events.
     * @param int $id The user id.
     * @param array $events A list of events.
     */
    protected function attachPostRequisitesToEvents(int $id, array $events): array
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

        // get post-requisites from offerings that a the user is associated with (as learner, instructor, etc.)
        $results = [];
        $joinsAndUserContexts = $this->getUserToOfferingJoinsAndUserContexts();
        $what = 'ps.id AS postRequisiteSessionId, c.id as courseId, s.id AS sessionId, school.id AS schoolId, ' .
            'o.id, o.startDate, o.endDate, o.room, o.url, o.updatedAt, o.updatedAt AS offeringUpdatedAt, ' .
            's.updatedAt AS sessionUpdatedAt, s.title, st.calendarColor, st.title as sessionTypeTitle, ' .
            's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
            's.attireRequired, s.equipmentRequired, s.supplemental, s.attendanceRequired, s.instructionalNotes, ' .
            'c.publishedAsTbd as coursePublishedAsTbd, c.published as coursePublished, c.title as courseTitle, ' .
            'c.level as courseLevel, st.id as sessionTypeId, ' .
            'c.externalId as courseExternalId, s.description AS sessionDescription';
        foreach ($joinsAndUserContexts as $joinsAndUserContext) {
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->addSelect($what)->from(User::class, 'u');
            foreach ($joinsAndUserContext[0] as $key => $statement) {
                $qb->leftJoin($statement, $key);
            }
            $qb->leftJoin('o.session', 's');
            $qb->leftJoin('s.course', 'c');
            $qb->leftJoin('c.school', 'school');
            $qb->leftJoin('s.sessionType', 'st');
            $qb->leftJoin('s.prerequisites', 'ps');
            $qb->where($qb->expr()->isNotNull('o.id'));
            $qb->andWhere($qb->expr()->in('ps.id', ':sessions'));
            $qb->andWhere($qb->expr()->eq('u.id', ':user_id'));
            $qb->setParameter('user_id', $id);
            $qb->setParameter('sessions', $sessionIds);

            $result = $qb->getQuery()->getArrayResult();
            if (!empty($result)) {
                $result = array_map(function ($event) use ($joinsAndUserContext) {
                    $event['userContexts'] = [$joinsAndUserContext[1]];
                    return $event;
                }, $result);
                $results = array_merge($results, $result);
            }
        }

        // dedupe results by offering id
        $dedupedResults = [];
        foreach ($results as $result) {
            if (array_key_exists($result['postRequisiteSessionId'], $dedupedResults)) {
                $dedupedResults[$result['postRequisiteSessionId']]['userContexts'] = array_unique(
                    array_merge(
                        $dedupedResults[$result['postRequisiteSessionId']]['userContexts'],
                        $result['userContexts']
                    )
                );
            } else {
                $dedupedResults[$result['postRequisiteSessionId']] = $result;
            }
        }
        $dedupedResults = array_values($dedupedResults);

        // create post-requisites events and attach them to their proper events
        foreach ($dedupedResults as $result) {
            $postrequisite = UserEvent::createFromCalendarEvent($id, $this->createEventObjectForOffering($result));
            $sessionId = $result['postRequisiteSessionId'];
            if (array_key_exists($sessionId, $sessionsMap)) {
                /** @var CalendarEvent $event */
                foreach ($sessionsMap[$sessionId] as $event) {
                    $event->postrequisites[] = $postrequisite;
                }
            }
        }

        // get post-requisites from ILMs that a the user is associated with (as learner, instructor, etc.)
        $results = [];
        $joinsAndUserContexts = $this->getUserToIlmJoinsAndUserContexts();
        $what = 'ps.id AS postRequisiteSessionId, c.id as courseId, s.id AS sessionId, school.id AS schoolId, ' .
            'ilm.id, ilm.dueDate, s.updatedAt, s.title, st.calendarColor, ' .
            's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
            's.attireRequired, s.equipmentRequired, s.supplemental, s.attendanceRequired, s.instructionalNotes, ' .
            'c.publishedAsTbd as coursePublishedAsTbd, c.published as coursePublished, c.title as courseTitle,' .
            'c.level as courseLevel, st.id as sessionTypeId, st.title AS sessionTypeTitle, ' .
            'c.externalId as courseExternalId, s.description AS sessionDescription';
        foreach ($joinsAndUserContexts as $joinsAndUserContext) {
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->addSelect($what)->from(User::class, 'u');
            foreach ($joinsAndUserContext[0] as $key => $statement) {
                $qb->leftJoin($statement, $key);
            }
            $qb->leftJoin('ilm.session', 's');
            $qb->leftJoin('s.course', 'c');
            $qb->leftJoin('c.school', 'school');
            $qb->leftJoin('s.sessionType', 'st');
            $qb->leftJoin('s.prerequisites', 'ps');
            $qb->where($qb->expr()->isNotNull('ilm.id'));
            $qb->andWhere($qb->expr()->in('ps.id', ':sessions'));
            $qb->andWhere($qb->expr()->eq('u.id', ':user_id'));
            $qb->setParameter('user_id', $id);
            $qb->setParameter('sessions', $sessionIds);

            $result = $qb->getQuery()->getArrayResult();
            if (!empty($result)) {
                $result = array_map(function ($event) use ($joinsAndUserContext) {
                    $event['userContexts'] = [$joinsAndUserContext[1]];
                    return $event;
                }, $result);
                $results = array_merge($results, $result);
            }
        }

        // dedupe results by ILM id
        $dedupedResults = [];
        foreach ($results as $result) {
            if (array_key_exists($result['postRequisiteSessionId'], $dedupedResults)) {
                $dedupedResults[$result['postRequisiteSessionId']]['userContexts'] = array_unique(
                    array_merge(
                        $dedupedResults[$result['postRequisiteSessionId']]['userContexts'],
                        $result['userContexts']
                    )
                );
            } else {
                $dedupedResults[$result['postRequisiteSessionId']] = $result;
            }
        }
        $dedupedResults = array_values($dedupedResults);

        // create post-requisites events and attach them to their proper events
        foreach ($dedupedResults as $result) {
            $postrequisite = UserEvent::createFromCalendarEvent(
                $id,
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
        if (array_key_exists('instructedCourses', $criteria)) {
            $ids = is_array($criteria['instructedCourses'])
                ? $criteria['instructedCourses'] : [$criteria['instructedCourses']];
            $qb->leftJoin('x.instructedOfferings', 'ic_offering');
            $qb->leftJoin('x.instructorIlmSessions', 'ic_ilm');
            $qb->leftJoin('x.instructorGroups', 'ic_iGroup');
            $qb->leftJoin('ic_iGroup.offerings', 'ic_offering2');
            $qb->leftJoin('ic_iGroup.ilmSessions', 'ic_ilm2');
            $qb->leftJoin('ic_offering.session', 'ic_session');
            $qb->leftJoin('ic_ilm.session', 'ic_session2');
            $qb->leftJoin('ic_offering2.session', 'ic_session3');
            $qb->leftJoin('ic_ilm2.session', 'ic_session4');
            $qb->leftJoin('ic_session.course', 'ic_course');
            $qb->leftJoin('ic_session2.course', 'ic_course2');
            $qb->leftJoin('ic_session3.course', 'ic_course3');
            $qb->leftJoin('ic_session4.course', 'ic_course4');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('ic_course.id', ':courses'),
                $qb->expr()->in('ic_course2.id', ':courses'),
                $qb->expr()->in('ic_course3.id', ':courses'),
                $qb->expr()->in('ic_course4.id', ':courses')
            ));
            $qb->setParameter(':courses', $ids);
        }

        if (array_key_exists('instructedSessions', $criteria)) {
            $ids = is_array($criteria['instructedSessions'])
                ? $criteria['instructedSessions'] : [$criteria['instructedSessions']];
            $qb->leftJoin('x.instructedOfferings', 'is_offering');
            $qb->leftJoin('x.instructorIlmSessions', 'is_ilm');
            $qb->leftJoin('x.instructorGroups', 'is_iGroup');
            $qb->leftJoin('is_iGroup.offerings', 'is_offering2');
            $qb->leftJoin('is_iGroup.ilmSessions', 'is_ilm2');
            $qb->leftJoin('is_offering.session', 'is_session');
            $qb->leftJoin('is_ilm.session', 'is_session2');
            $qb->leftJoin('is_offering2.session', 'is_session3');
            $qb->leftJoin('is_ilm2.session', 'is_session4');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('is_session.id', ':sessions'),
                $qb->expr()->in('is_session2.id', ':sessions'),
                $qb->expr()->in('is_session3.id', ':sessions'),
                $qb->expr()->in('is_session4.id', ':sessions')
            ));
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('instructedLearningMaterials', $criteria)) {
            $ids = is_array($criteria['instructedLearningMaterials']) ?
                $criteria['instructedLearningMaterials'] : [$criteria['instructedLearningMaterials']];
            $qb->leftJoin('x.instructedOfferings', 'ilm_offering');
            $qb->leftJoin('x.instructorIlmSessions', 'ilm_ilm');
            $qb->leftJoin('x.instructorGroups', 'ilm_iGroup');
            $qb->leftJoin('ilm_iGroup.offerings', 'ilm_offering2');
            $qb->leftJoin('ilm_iGroup.ilmSessions', 'ilm_ilm2');
            $qb->leftJoin('ilm_offering.session', 'ilm_session');
            $qb->leftJoin('ilm_ilm.session', 'ilm_session2');
            $qb->leftJoin('ilm_offering2.session', 'ilm_session3');
            $qb->leftJoin('ilm_ilm2.session', 'ilm_session4');
            $qb->leftJoin('ilm_session.learningMaterials', 'ilm_slm');
            $qb->leftJoin('ilm_session2.learningMaterials', 'ilm_slm2');
            $qb->leftJoin('ilm_session3.learningMaterials', 'ilm_slm3');
            $qb->leftJoin('ilm_session4.learningMaterials', 'ilm_slm4');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('ilm_slm.id', ':learningMaterials'),
                $qb->expr()->in('ilm_slm2.id', ':learningMaterials'),
                $qb->expr()->in('ilm_slm3.id', ':learningMaterials'),
                $qb->expr()->in('ilm_slm4.id', ':learningMaterials')
            ));
            $qb->setParameter(':learningMaterials', $ids);
        }

        if (array_key_exists('instructedTerms', $criteria)) {
            $ids = is_array($criteria['instructedTerms'])
                ? $criteria['instructedTerms'] : [$criteria['instructedTerms']];
            $qb->leftJoin('x.instructedOfferings', 'it_offering');
            $qb->leftJoin('x.instructorIlmSessions', 'it_ilm');
            $qb->leftJoin('x.instructorGroups', 'it_iGroup');
            $qb->leftJoin('it_iGroup.offerings', 'it_offering2');
            $qb->leftJoin('it_iGroup.ilmSessions', 'it_ilm2');
            $qb->leftJoin('it_offering.session', 'it_session');
            $qb->leftJoin('it_ilm.session', 'it_session2');
            $qb->leftJoin('it_offering2.session', 'it_session3');
            $qb->leftJoin('it_ilm2.session', 'it_session4');
            $qb->leftJoin('it_session.terms', 'it_term');
            $qb->leftJoin('it_session2.terms', 'it_term2');
            $qb->leftJoin('it_session3.terms', 'it_term3');
            $qb->leftJoin('it_session4.terms', 'it_term4');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('it_term.id', ':terms'),
                $qb->expr()->in('it_term2.id', ':terms'),
                $qb->expr()->in('it_term3.id', ':terms'),
                $qb->expr()->in('it_term4.id', ':terms')
            ));
            $qb->setParameter(':terms', $ids);
        }

        if (array_key_exists('instructedSessionTypes', $criteria)) {
            $ids = is_array($criteria['instructedSessionTypes']) ?
                $criteria['instructedSessionTypes'] : [$criteria['instructedSessionTypes']];
            $qb->leftJoin('x.instructedOfferings', 'ist_offering');
            $qb->leftJoin('x.instructorIlmSessions', 'ist_ilm');
            $qb->leftJoin('x.instructorGroups', 'ist_iGroup');
            $qb->leftJoin('ist_iGroup.offerings', 'ist_offering2');
            $qb->leftJoin('ist_iGroup.ilmSessions', 'ist_ilm2');
            $qb->leftJoin('ist_offering.session', 'ist_session');
            $qb->leftJoin('ist_ilm.session', 'ist_session2');
            $qb->leftJoin('ist_offering2.session', 'ist_session3');
            $qb->leftJoin('ist_ilm2.session', 'ist_session4');
            $qb->leftJoin('ist_session.sessionType', 'ist_sessionType');
            $qb->leftJoin('ist_session2.sessionType', 'ist_sessionType2');
            $qb->leftJoin('ist_session3.sessionType', 'ist_sessionType3');
            $qb->leftJoin('ist_session4.sessionType', 'ist_sessionType4');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('ist_sessionType.id', ':sessionTypes'),
                $qb->expr()->in('ist_sessionType2.id', ':sessionTypes'),
                $qb->expr()->in('ist_sessionType3.id', ':sessionTypes'),
                $qb->expr()->in('ist_sessionType4.id', ':sessionTypes')
            ));
            $qb->setParameter(':sessionTypes', $ids);
        }

        if (array_key_exists('instructorGroups', $criteria)) {
            $ids = is_array($criteria['instructorGroups'])
                ? $criteria['instructorGroups'] : [$criteria['instructorGroups']];
            $qb->join('x.instructorGroups', 'ig_iGroup');
            $qb->andWhere($qb->expr()->in('ig_iGroup.id', ':instructorGroups'));
            $qb->setParameter(':instructorGroups', $ids);
        }

        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools'])
                ? $criteria['schools'] : [$criteria['schools']];
            $qb->join('x.school', 'sc_school');
            $qb->andWhere($qb->expr()->in('sc_school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
        }

        if (array_key_exists('roles', $criteria)) {
            $ids = is_array($criteria['roles'])
                ? $criteria['roles'] : [$criteria['roles']];
            $qb->join('x.roles', 'r_roles');
            $qb->andWhere($qb->expr()->in('r_roles.id', ':roles'));
            $qb->setParameter(':roles', $ids);
        }

        if (array_key_exists('cohorts', $criteria)) {
            $ids = is_array($criteria['cohorts'])
                ? $criteria['cohorts'] : [$criteria['cohorts']];
            if (in_array(null, $ids)) {
                $ids = array_diff($ids, [null]);
                $qb->andWhere('x.cohorts IS EMPTY');
            }
            if ($ids !== []) {
                $qb->join('x.cohorts', 'c_cohorts');
                $qb->andWhere($qb->expr()->in('c_cohorts.id', ':cohorts'));
                $qb->setParameter(':cohorts', $ids);
            }
        }

        if (array_key_exists('learnerSessions', $criteria)) {
            $ids = is_array($criteria['learnerSessions'])
                ? $criteria['learnerSessions'] : [$criteria['learnerSessions']];

            $qb->leftJoin('x.offerings', 'ls_offering');
            $qb->leftJoin('x.learnerIlmSessions', 'ls_ilm');
            $qb->leftJoin('x.learnerGroups', 'ls_iGroup');
            $qb->leftJoin('ls_iGroup.offerings', 'ls_offering2');
            $qb->leftJoin('ls_iGroup.ilmSessions', 'ls_ilm2');
            $qb->leftJoin('ls_offering.session', 'ls_session');
            $qb->leftJoin('ls_ilm.session', 'ls_session2');
            $qb->leftJoin('ls_offering2.session', 'ls_session3');
            $qb->leftJoin('ls_ilm2.session', 'ls_session4');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('ls_session.id', ':sessions'),
                $qb->expr()->in('ls_session2.id', ':sessions'),
                $qb->expr()->in('ls_session3.id', ':sessions'),
                $qb->expr()->in('ls_session4.id', ':sessions')
            ));
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('instructedAcademicYears', $criteria)) {
            $ids = is_array($criteria['instructedAcademicYears'])
                ? $criteria['instructedAcademicYears'] : [$criteria['instructedAcademicYears']];
            $qb->leftJoin('x.instructedOfferings', 'y_offering');
            $qb->leftJoin('x.instructorIlmSessions', 'y_ilm');
            $qb->leftJoin('x.instructorGroups', 'y_iGroup');
            $qb->leftJoin('y_iGroup.offerings', 'y_offering2');
            $qb->leftJoin('y_iGroup.ilmSessions', 'y_ilm2');
            $qb->leftJoin('y_offering.session', 'y_session');
            $qb->leftJoin('y_ilm.session', 'y_session2');
            $qb->leftJoin('y_offering2.session', 'y_session3');
            $qb->leftJoin('y_ilm2.session', 'y_session4');
            $qb->leftJoin('y_session.course', 'y_course');
            $qb->leftJoin('y_session2.course', 'y_course2');
            $qb->leftJoin('y_session3.course', 'y_course3');
            $qb->leftJoin('y_session4.course', 'y_course4');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('y_course.year', ':years'),
                $qb->expr()->in('y_course2.year', ':years'),
                $qb->expr()->in('y_course3.year', ':years'),
                $qb->expr()->in('y_course4.year', ':years')
            ));
            $qb->setParameter(':years', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['cohorts']);
        unset($criteria['roles']);
        unset($criteria['schools']);
        unset($criteria['instructedCourses']);
        unset($criteria['instructedSessions']);
        unset($criteria['instructedLearningMaterials']);
        unset($criteria['instructedTerms']);
        unset($criteria['instructedSessionTypes']);
        unset($criteria['instructorGroups']);
        unset($criteria['learnerSessions']);
        unset($criteria['instructedAcademicYears']);

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }

    protected function createUserDTOs(AbstractQuery $query): array
    {
        $dtos = [];
        foreach ($query->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new UserDTO(
                $arr['id'],
                $arr['firstName'],
                $arr['lastName'],
                $arr['middleName'],
                $arr['displayName'],
                $arr['phone'],
                $arr['email'],
                $arr['preferredEmail'],
                $arr['pronouns'],
                $arr['addedViaIlios'],
                $arr['enabled'],
                $arr['campusId'],
                $arr['otherId'],
                $arr['examined'],
                $arr['userSyncIgnore'],
                $arr['icsFeedKey'],
                $arr['root']
            );
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(
            'u.id AS userId, c.id AS primaryCohortId, s.id AS schoolId, '
            . 'auser.id as authenticationId, a.username as username'
        )
            ->from(User::class, 'u')
            ->join('u.school', 's')
            ->leftJoin('u.primaryCohort', 'c')
            ->leftJoin('u.authentication', 'a')
            ->leftJoin('a.user', 'auser')
            ->where($qb->expr()->in('u.id', ':userIds'))
            ->setParameter('userIds', array_keys($dtos));

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['userId']]->primaryCohort = $arr['primaryCohortId'] ?: null;
            $dtos[$arr['userId']]->school = $arr['schoolId'];
            $dtos[$arr['userId']]->authentication = $arr['authenticationId'] ?: null;
            $dtos[$arr['userId']]->username = $arr['username'] ?: null;
        }

        $dtos = $this->attachRelatedToDtos(
            $dtos,
            [
                'directedCourses',
                'administeredCourses',
                'studentAdvisedCourses',
                'learnerGroups',
                'instructedLearnerGroups',
                'instructorGroups',
                'offerings',
                'instructedOfferings',
                'instructorIlmSessions',
                'programYears',
                'roles',
                'reports',
                'cohorts',
                'pendingUserUpdates',
                'auditLogs',
                'learnerIlmSessions',
                'directedSchools',
                'administeredSchools',
                'administeredSessions',
                'studentAdvisedSessions',
                'directedPrograms',
                'administeredCurriculumInventoryReports',
                'sessionMaterialStatuses',
            ],
        );

        return array_values($dtos);
    }

    /**
     * Find all of the assigned materials for a user
     */
    public function findMaterialsForUser(int $id, array $criteria): array
    {
        $factory = $this->factory;
        $offIdQb = $this->getEntityManager()->createQueryBuilder();
        $offIdQb->select('learnerOffering.id')->from(User::class, 'learnerU');
        $offIdQb->join('learnerU.offerings', 'learnerOffering');
        $offIdQb->andWhere($offIdQb->expr()->eq('learnerU.id', ':user_id'));

        $groupOfferingQb = $this->getEntityManager()->createQueryBuilder();
        $groupOfferingQb->select('groupOffering.id')->from(User::class, 'groupU');
        $groupOfferingQb->leftJoin('groupU.learnerGroups', 'g');
        $groupOfferingQb->leftJoin('g.offerings', 'groupOffering');
        $groupOfferingQb->andWhere($groupOfferingQb->expr()->eq('groupU.id', ':user_id'));

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('s.id, o.startDate, o.id as offeringId');
        $qb->from(Offering::class, 'o');
        $qb->join('o.session', 's');
        $qb->where($qb->expr()->orX(
            $qb->expr()->in('o.id', $offIdQb->getDQL()),
            $qb->expr()->in('o.id', $groupOfferingQb->getDQL())
        ));
        if (array_key_exists('before', $criteria)) {
            $qb->andWhere($qb->expr()->lte('o.startDate', ':before'));
            $qb->setParameter('before', $criteria['before']);
        }
        if (array_key_exists('after', $criteria)) {
            $qb->andWhere($qb->expr()->gte('o.startDate', ':after'));
            $qb->setParameter('after', $criteria['after']);
        }
        $qb->setParameter('user_id', $id);

        $offeringSessions = $qb->getQuery()->getArrayResult();

        $ilmQb = $this->getEntityManager()->createQueryBuilder();
        $ilmQb->select('learnerIlmSession.id')->from(User::class, 'learnerU');
        $ilmQb->join('learnerU.learnerIlmSessions', 'learnerIlmSession');
        $ilmQb->andWhere($ilmQb->expr()->eq('learnerU.id', ':user_id'));

        $groupIlmSessionQb = $this->getEntityManager()->createQueryBuilder();
        $groupIlmSessionQb->select('groupIlmSession.id')->from(User::class, 'groupU');
        $groupIlmSessionQb->leftJoin('groupU.learnerGroups', 'g');
        $groupIlmSessionQb->leftJoin('g.ilmSessions', 'groupIlmSession');
        $groupIlmSessionQb->andWhere($groupIlmSessionQb->expr()->eq('groupU.id', ':user_id'));

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('s.id, ilm.dueDate, ilm.id AS ilmId');
        $qb->from(IlmSession::class, 'ilm');
        $qb->join('ilm.session', 's');
        $qb->where($qb->expr()->orX(
            $qb->expr()->in('ilm.id', $ilmQb->getDQL()),
            $qb->expr()->in('ilm.id', $groupIlmSessionQb->getDQL())
        ));
        if (array_key_exists('before', $criteria)) {
            $qb->andWhere($qb->expr()->lte('ilm.dueDate', ':before'));
            $qb->setParameter('before', $criteria['before']);
        }
        if (array_key_exists('after', $criteria)) {
            $qb->andWhere($qb->expr()->gte('ilm.dueDate', ':after'));
            $qb->setParameter('after', $criteria['after']);
        }
        $qb->setParameter('user_id', $id);

        $ilmSessions = $qb->getQuery()->getArrayResult();
        $offeringIds = array_map(fn(array $arr) => $arr['offeringId'], $offeringSessions);
        $ilmIds = array_map(fn(array $arr) => $arr['ilmId'], $ilmSessions);
        $offeringInstructors = $this->getInstructorsForOfferings($offeringIds, $this->getEntityManager());
        $ilmInstructors = $this->getInstructorsForIlmSessions($ilmIds, $this->getEntityManager());

        $sessions = [];
        foreach ($offeringSessions as $arr) {
            if (!array_key_exists($arr['id'], $sessions)) {
                $sessions[$arr['id']] = [
                    'firstOfferingDate' => $arr['startDate'],
                    'instructors' => [],
                ];
            }
            if ($arr['startDate'] < $sessions[$arr['id']]['firstOfferingDate']) {
                $sessions[$arr['id']]['firstOfferingDate'] = $arr['startDate'];
            }
            if (array_key_exists($arr['offeringId'], $offeringInstructors)) {
                $sessions[$arr['id']]['instructors'] = array_values($offeringInstructors[$arr['offeringId']]);
            }
        }
        foreach ($ilmSessions as $arr) {
            if (!array_key_exists($arr['id'], $sessions)) {
                $sessions[$arr['id']] = [
                    'firstOfferingDate' => $arr['dueDate'],
                    'instructors' => [],
                ];
            }
            if ($arr['dueDate'] < $sessions[$arr['id']]['firstOfferingDate']) {
                $sessions[$arr['id']]['firstOfferingDate'] = $arr['dueDate'];
            }
            if (array_key_exists($arr['ilmId'], $ilmInstructors)) {
                $sessions[$arr['id']]['instructors'] = array_values($ilmInstructors[$arr['ilmId']]);
            }
        }
        $sessionIds = array_keys($sessions);


        $sessionMaterials = $this->getSessionLearningMaterialsForPublishedSessions(
            $sessionIds,
            $this->getEntityManager()
        );

        $sessionUserMaterials = array_map(function (array $arr) use ($factory, $sessions) {
            $arr['firstOfferingDate'] = $sessions[$arr['sessionId']]['firstOfferingDate'];
            $arr['instructors'] = $sessions[$arr['sessionId']]['instructors'];
            return $factory->create($arr);
        }, $sessionMaterials);

        $courseMaterials = $this->getCourseLearningMaterialsForPublishedSessions(
            $sessionIds,
            $this->getEntityManager()
        );

        $courseUserMaterials = array_map(fn(array $arr) => $factory->create($arr), $courseMaterials);


        $userMaterials = array_merge($sessionUserMaterials, $courseUserMaterials);
        //sort materials by id for consistency
        usort($userMaterials, fn(UserMaterial $a, UserMaterial $b) => $a->id - $b->id);

        return $userMaterials;
    }

    /**
     * Finds and adds learning materials to a given list of calendar events.
     *
     * @param UserEvent[] $events
     */
    public function addMaterialsToEvents(array $events): array
    {
        return $this->attachMaterialsToEvents($events, $this->factory, $this->getEntityManager());
    }

    /**
     * Finds and adds course- and session-objectives and their competencies to a given list of calendar events.
     *
     * @param UserEvent[] $events
     */
    public function addSessionDataToEvents(array $events): array
    {
        return $this->attachSessionDataToEvents($events, $this->getEntityManager());
    }

    /**
     * Returns a list of ids of schools directed by the given user.
     */
    public function getDirectedSchoolIds(int $userId): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('school.id')->distinct()->from(User::class, 'u');
        $qb->join('u.directedSchools', 'school');
        $qb->andWhere($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        return array_column($qb->getQuery()->getArrayResult(), 'id');
    }

    /**
     * Returns a list of ids of schools administered by the given user.
     */
    public function getAdministeredSchoolIds(int $userId): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('school.id')->distinct()->from(User::class, 'u');
        $qb->join('u.administeredSchools', 'school');
        $qb->andWhere($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        return array_column($qb->getQuery()->getArrayResult(), 'id');
    }

    /**
     * Returns an assoc. array of ids os courses directed by the given user,
     * and the ids of schools owning these directed courses.
     */
    public function getDirectedCourseAndSchoolIds(int $userId): array
    {
        $rhett['schoolIds'] = [];
        $rhett['courseIds'] = [];

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('school.id as schoolId, courses.id as courseId')
            ->distinct()
            ->from(User::class, 'u');
        $qb->join('u.directedCourses', 'courses');
        $qb->join('courses.school', 'school');
        $qb->andWhere($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        foreach ($qb->getQuery()->getArrayResult() as $arr) {
            $rhett['schoolIds'][] = $arr['schoolId'];
            $rhett['courseIds'][] = $arr['courseId'];
        }

        return $this->dedupeSubArrays($rhett);
    }

    /**
     * Returns an assoc. array of ids of courses administered by the given user,
     * and the ids of schools owning these administered courses.
     */
    public function getAdministeredCourseAndSchoolIds(int $userId): array
    {
        $rhett['schoolIds'] = [];
        $rhett['courseIds'] = [];

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('school.id as schoolId, courses.id as courseId')
            ->distinct()
            ->from(User::class, 'u');
        $qb->join('u.administeredCourses', 'courses');
        $qb->join('courses.school', 'school');
        $qb->andWhere($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        foreach ($qb->getQuery()->getArrayResult() as $arr) {
            $rhett['schoolIds'][] = $arr['schoolId'];
            $rhett['courseIds'][] = $arr['courseId'];
        }

        return $this->dedupeSubArrays($rhett);
    }

    /**
     * Returns an assoc. array of ids of curriculum inventory reports administered by the given user,
     * and the ids of schools owning these administered reports.
     */
    public function getAdministeredCurriculumInventoryReportAndSchoolIds(int $userId): array
    {
        $rhett['reportIds'] = [];
        $rhett['schoolIds'] = [];

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('school.id as schoolId, ciReports.id as ciReportId')
            ->distinct()
            ->from(User::class, 'u');
        $qb->join('u.administeredCurriculumInventoryReports', 'ciReports');
        $qb->join('ciReports.program', 'program');
        $qb->join('program.school', 'school');
        $qb->andWhere($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        foreach ($qb->getQuery()->getArrayResult() as $arr) {
            $rhett['reportIds'][] = $arr['ciReportId'];
            $rhett['schoolIds'][] = $arr['schoolId'];
        }

        return $this->dedupeSubArrays($rhett);
    }

    /**
     * Returns an assoc. array of ids of sessions administered by the given user,
     * and the ids of schools and courses owning these administered sessions.
     */
    public function getAdministeredSessionCourseAndSchoolIds(int $userId): array
    {
        $rhett['schoolIds'] = [];
        $rhett['courseIds'] = [];
        $rhett['sessionIds'] = [];

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('school.id as schoolId, course.id as courseId, session.id as sessionId')->distinct();
        $qb->from(User::class, 'u');
        $qb->join('u.administeredSessions', 'session');
        $qb->join('session.course', 'course');
        $qb->join('course.school', 'school');
        $qb->andWhere($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        foreach ($qb->getQuery()->getArrayResult() as $arr) {
            $rhett['schoolIds'][] = $arr['schoolId'];
            $rhett['courseIds'][] = $arr['courseId'];
            $rhett['sessionIds'][] = $arr['sessionId'];
        }

        return $this->dedupeSubArrays($rhett);
    }

    /*
     * Returns an assoc. array of ids of sessions and course ids that a user
     * is a student advisor in.
     */
    public function getStudentAdvisedSessionAndCourseIds(int $userId): array
    {
        $rhett['courseIds'] = [];
        $rhett['sessionIds'] = [];

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('course.id as courseId, session.id as sessionId')->distinct();
        $qb->from(User::class, 'u');
        $qb->leftJoin('u.studentAdvisedSessions', 'session');
        $qb->leftJoin('u.studentAdvisedCourses', 'course');
        $qb->andWhere($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        foreach ($qb->getQuery()->getArrayResult() as $arr) {
            $rhett['courseIds'][] = $arr['courseId'];
            $rhett['sessionIds'][] = $arr['sessionId'];
        }

        return $this->dedupeSubArrays($rhett);
    }

    /*
     * Returns an assoc. array of ids of offering and ILM ids that a student is connected to
     */
    public function getLearnerIlmAndOfferingIds(int $userId): array
    {
        $rhett['offeringIds'] = [];
        $rhett['ilmIds'] = [];

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('offering.id as o1Id, ilm.id as i1id, offering2.id as o2Id, ilm2.id as i2id')->distinct();
        $qb->from(User::class, 'u');
        $qb->leftJoin('u.offerings', 'offering');
        $qb->leftJoin('u.learnerIlmSessions', 'ilm');
        $qb->leftJoin('u.learnerGroups', 'learnerGroup');
        $qb->leftJoin('learnerGroup.offerings', 'offering2');
        $qb->leftJoin('learnerGroup.ilmSessions', 'ilm2');
        $qb->andWhere($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        foreach ($qb->getQuery()->getArrayResult() as $arr) {
            $rhett['offeringIds'][] = $arr['o1Id'];
            $rhett['ilmIds'][] = $arr['i1id'];
            $rhett['offeringIds'][] = $arr['o2Id'];
            $rhett['ilmIds'][] = $arr['i2id'];
        }

        return $this->dedupeSubArrays($rhett);
    }

    /*
     * Returns an array of ids of session ids that a student is connected to
     */
    public function getLearnerSessionIds(int $userId): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('s1.id AS s1Id, s2.id AS s2Id, s3.id AS s3Id, s4.id AS s4Id')->distinct();
        $qb->from(User::class, 'u');
        $qb->leftJoin('u.offerings', 'offering');
        $qb->leftJoin('u.learnerIlmSessions', 'ilm');
        $qb->leftJoin('u.learnerGroups', 'learnerGroup');
        $qb->leftJoin('learnerGroup.offerings', 'offering2');
        $qb->leftJoin('learnerGroup.ilmSessions', 'ilm2');
        $qb->leftJoin('offering.session', 's1');
        $qb->leftJoin('ilm.session', 's2');
        $qb->leftJoin('offering2.session', 's3');
        $qb->leftJoin('ilm2.session', 's4');
        $qb->andWhere($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        $rhett = [];
        foreach ($qb->getQuery()->getArrayResult() as $arr) {
            foreach ($arr as $id) {
                if (!is_null($id)) {
                    $rhett[] = $id;
                }
            }
        }

        return array_unique($rhett);
    }

    /**
     * Returns a list of ids of schools which own learner groups instructed by the given user.
     */
    public function getInstructedLearnerGroupSchoolIds(int $userId): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('school.id')->distinct()->from(User::class, 'u');
        $qb->join('u.instructedLearnerGroups', 'instructedLearnerGroups');
        $qb->join('instructedLearnerGroups.cohort', 'cohort');
        $qb->join('cohort.programYear', 'programYear');
        $qb->join('programYear.program', 'program');
        $qb->join('program.school', 'school');
        $qb->andWhere($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        return array_column($qb->getQuery()->getArrayResult(), 'id');
    }

    /**
     * Returns a list of ids of learner groups that the given user is a member of.
     */
    public function getLearnerGroupIds(int $userId): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('learnerGroups.id')->distinct()->from(User::class, 'u');
        $qb->join('u.learnerGroups', 'learnerGroups');
        $qb->where($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        return array_column($qb->getQuery()->getArrayResult(), 'id');
    }

    /**
     * Returns a list of ids of instructor groups that the given user is a member of.
     */
    public function getInstructorGroupIds(int $userId): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('instructorGroups.id')->distinct()->from(User::class, 'u');
        $qb->join('u.instructorGroups', 'instructorGroups');
        $qb->where($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        return array_column($qb->getQuery()->getArrayResult(), 'id');
    }

    /**
     * Returns a list of ids of schools owning instructor groups that the given user is part of.
     */
    public function getInstructorGroupSchoolIds(int $userId): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('school.id')->distinct()->from(User::class, 'u');
        $qb->join('u.instructorGroups', 'instructorGroup');
        $qb->join('instructorGroup.school', 'school');
        $qb->andWhere($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        return array_column($qb->getQuery()->getArrayResult(), 'id');
    }

    /**
     * Returns an assoc. array of ids of courses that are linked to the programs directed by the given user,
     * and the ids of cohorts, program years and directed programs in this chain of associations.
     */
    public function getCoursesCohortsProgramYearAndProgramIdsLinkedToProgramsDirectedByUser(int $userId): array
    {
        $rhett['programIds'] = [];
        $rhett['programYearIds'] = [];
        $rhett['cohortIds'] = [];
        $rhett['courseIds'] = [];

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(
            'course.id as courseId, cohort.id as cohortId, programYear.id as programYearId, program.id as programId'
        )->distinct()->from(User::class, 'u');
        $qb->join('u.directedPrograms', 'program');
        $qb->join('program.programYears', 'programYear');
        $qb->join('programYear.cohort', 'cohort');
        $qb->join('cohort.courses', 'course');
        $qb->where($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        foreach ($qb->getQuery()->getArrayResult() as $arr) {
            $rhett['programIds'][] = $arr['programId'];
            $rhett['programYearIds'][] = $arr['programYearId'];
            $rhett['cohortIds'][] = $arr['cohortId'];
            $rhett['courseIds'] [] = $arr['courseId'];
        }

        return $this->dedupeSubArrays($rhett);
    }

    /**
     * Returns an assoc. array of ids of ILMs and offerings instructed by the given user,
     * and the ids of schools, courses, and sessions owning these instructed and offerings.
     */
    public function getInstructedOfferingIlmSessionCourseAndSchoolIds(int $userId): array
    {
        $rhett['schoolIds'] = [];
        $rhett['courseIds'] = [];
        $rhett['sessionIds'] = [];
        $rhett['ilmIds'] = [];
        $rhett['offeringIds'] = [];

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('o.id as offeringId, session.id as sessionId, course.id as courseId, school.id as schoolId')
            ->distinct()
            ->from(User::class, 'u');
        $qb->join('u.instructorGroups', 'instructorGroup');
        $qb->join('instructorGroup.offerings', 'o');
        $qb->join('o.session', 'session');
        $qb->join('session.course', 'course');
        $qb->join('course.school', 'school');
        $qb->andWhere($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        foreach ($qb->getQuery()->getArrayResult() as $arr) {
            $rhett['schoolIds'][] = $arr['schoolId'];
            $rhett['courseIds'][] = $arr['courseId'];
            $rhett['sessionIds'][] = $arr['sessionId'];
            $rhett['offeringIds'][] = $arr['offeringId'];
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('ilm.id as ilmId, session.id as sessionId, course.id as courseId, school.id as schoolId')
           ->distinct()
           ->from(User::class, 'u');
        $qb->join('u.instructorGroups', 'instructorGroup');
        $qb->join('instructorGroup.ilmSessions', 'ilm');
        $qb->join('ilm.session', 'session');
        $qb->join('session.course', 'course');
        $qb->join('course.school', 'school');
        $qb->andWhere($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        foreach ($qb->getQuery()->getArrayResult() as $arr) {
            $rhett['schoolIds'][] = $arr['schoolId'];
            $rhett['courseIds'][] = $arr['courseId'];
            $rhett['sessionIds'][] = $arr['sessionId'];
            $rhett['ilmIds'][] = $arr['ilmId'];
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('o.id as offeringId, session.id as sessionId, course.id as courseId, school.id as schoolId')
            ->distinct()
            ->from(User::class, 'u');
        $qb->join('u.instructedOfferings', 'o');
        $qb->join('o.session', 'session');
        $qb->join('session.course', 'course');
        $qb->join('course.school', 'school');
        $qb->andWhere($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        foreach ($qb->getQuery()->getArrayResult() as $arr) {
            $rhett['schoolIds'][] = $arr['schoolId'];
            $rhett['courseIds'][] = $arr['courseId'];
            $rhett['sessionIds'][] = $arr['sessionId'];
            $rhett['offeringIds'][] = $arr['offeringId'];
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('ilm.id as ilmId, session.id as sessionId, course.id as courseId, school.id as schoolId')
           ->distinct()
           ->from(User::class, 'u');
        $qb->join('u.instructorIlmSessions', 'ilm');
        $qb->join('ilm.session', 'session');
        $qb->join('session.course', 'course');
        $qb->join('course.school', 'school');
        $qb->andWhere($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        foreach ($qb->getQuery()->getArrayResult() as $arr) {
            $rhett['schoolIds'][] = $arr['schoolId'];
            $rhett['courseIds'][] = $arr['courseId'];
            $rhett['sessionIds'][] = $arr['sessionId'];
            $rhett['ilmIds'][] = $arr['ilmId'];
        }

        return $this->dedupeSubArrays($rhett);
    }

    /**
     * Returns an assoc. array of ids of programs directed by the given user,
     * and the ids of schools owning these directed programs.
     */
    public function getDirectedProgramAndSchoolIds(int $userId): array
    {
        $rhett['programIds'] = [];
        $rhett['schoolIds'] = [];

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('school.id as schoolId, program.id as programId')
            ->distinct()
            ->from(User::class, 'u');
        $qb->join('u.directedPrograms', 'program');
        $qb->join('program.school', 'school');
        $qb->andWhere($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);
        foreach ($qb->getQuery()->getArrayResult() as $arr) {
            $rhett['programIds'][] = $arr['programId'];
            $rhett['schoolIds'][] = $arr['schoolId'];
        }

        return $this->dedupeSubArrays($rhett);
    }

    /**
     * Returns an assoc. array of ids of program-years directed by the given user,
     * and the ids of programs and schools owning these directed program years.
     */
    public function getDirectedProgramYearProgramAndSchoolIds(int $userId): array
    {
        $rhett['programYearIds'] = [];
        $rhett['programIds'] = [];
        $rhett['schoolIds'] = [];

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('school.id as schoolId, program.id as programId, py.id as pyId')->distinct();
        $qb->from(User::class, 'u');
        $qb->join('u.programYears', 'py');
        $qb->join('py.program', 'program');
        $qb->join('program.school', 'school');
        $qb->andWhere($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        foreach ($qb->getQuery()->getArrayResult() as $arr) {
            $rhett['programYearIds'][] = $arr['pyId'];
            $rhett['programIds'][] = $arr['programId'];
            $rhett['schoolIds'][] = $arr['schoolId'];
        }

        return $this->dedupeSubArrays($rhett);
    }

    /**
     * De-dupes entries in sub-arrays of a given associative array.
     */
    protected function dedupeSubArrays(array $map): array
    {
        $rhett = [];

        foreach ($map as $key => $value) {
            if (is_array($value)) {
                $value = array_unique($value);
            }
            $rhett[$key] = $value;
        }

        return $rhett;
    }

    protected function getUserToIlmJoinsAndUserContexts(): array
    {
        return [
            [['g' => 'u.learnerGroups', 'ilm' => 'g.ilmSessions'], CalendarEventUserContext::LEARNER],
            [['g' => 'u.instructorGroups', 'ilm' => 'g.ilmSessions'], CalendarEventUserContext::INSTRUCTOR],
            [['ilm' => 'u.learnerIlmSessions'], CalendarEventUserContext::LEARNER],
            [['ilm' => 'u.instructorIlmSessions'], CalendarEventUserContext::INSTRUCTOR],
            [
                ['dc' => 'u.directedCourses', 'sess' => 'dc.sessions', 'ilm' => 'sess.ilmSession'],
                CalendarEventUserContext::COURSE_DIRECTOR,
            ],
            [
                ['ac' => 'u.administeredCourses', 'sess' => 'ac.sessions', 'ilm' => 'sess.ilmSession'],
                CalendarEventUserContext::COURSE_ADMINISTRATOR,
            ],
            [
                ['sess' => 'u.administeredSessions', 'ilm' => 'sess.ilmSession'],
                CalendarEventUserContext::SESSION_ADMINISTRATOR,
            ],
        ];
    }

    protected function getUserToOfferingJoinsAndUserContexts(): array
    {
        return [
            [['g' => 'u.learnerGroups', 'o' => 'g.offerings'], CalendarEventUserContext::LEARNER],
            [['g' => 'u.instructorGroups', 'o' => 'g.offerings'], CalendarEventUserContext::INSTRUCTOR],
            [['o' => 'u.offerings'], CalendarEventUserContext::LEARNER],
            [['o' => 'u.instructedOfferings'], CalendarEventUserContext::INSTRUCTOR],
            [
                ['dc' => 'u.directedCourses', 'dcs' => 'dc.sessions', 'o' => 'dcs.offerings'],
                CalendarEventUserContext::COURSE_DIRECTOR,
            ],
            [
                ['ac' => 'u.administeredCourses', 'acs' => 'ac.sessions', 'o' => 'acs.offerings'],
                CalendarEventUserContext::COURSE_ADMINISTRATOR,
            ],
            [
                ['sess' => 'u.administeredSessions', 'o' => 'sess.offerings'],
                CalendarEventUserContext::SESSION_ADMINISTRATOR,
            ],

        ];
    }
}
