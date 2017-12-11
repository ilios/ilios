<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\DBAL\Types\Type as DoctrineType;
use Ilios\CoreBundle\Classes\CalendarEvent;
use Ilios\CoreBundle\Classes\UserEvent;
use Ilios\CoreBundle\Classes\UserMaterial;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Entity\DTO\UserDTO;
use Ilios\CoreBundle\Service\UserMaterialFactory;

/**
 * Class UserRepository
 */
class UserRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT u')->from('IliosCoreBundle:User', 'u');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find by a string query
     * @param string $q
     * @param integer $orderBy
     * @param integer $limit
     * @param integer $offset
     * @param array $criteria
     * @return UserInterface[]
     */
    public function findByQ($q, $orderBy, $limit, $offset, array $criteria = array())
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->add('select', 'u')->from('IliosCoreBundle:User', 'u');
        $qb->leftJoin('u.authentication', 'auth');

        $terms = explode(' ', $q);
        $terms = array_filter($terms, 'strlen');
        if (empty($terms)) {
            return [];
        }

        foreach ($terms as $key => $term) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('u.firstName', "?{$key}"),
                $qb->expr()->like('u.lastName', "?{$key}"),
                $qb->expr()->like('u.middleName', "?{$key}"),
                $qb->expr()->like('u.email', "?{$key}"),
                $qb->expr()->like('u.campusId', "?{$key}"),
                $qb->expr()->like('auth.username', "?{$key}")
            ))
                ->setParameter($key, '%' . $term . '%');
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('u.' . $sort, $order);
            }
        }
        if (array_key_exists('roles', $criteria)) {
            $roleIds = is_array($criteria['roles']) ? $criteria['roles'] : [$criteria['roles']];
            $qb->join('u.roles', 'r');
            $qb->andWhere($qb->expr()->in('r.id', ':roles'));
            $qb->setParameter(':roles', $roleIds);
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

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
     * @return UserDTO[]
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder()->select('u')->distinct()->from('IliosCoreBundle:User', 'u');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        return $this->createUserDTOs($qb->getQuery());
    }

    /**
     * Find and hydrate as DTOs
     *
     * @param array $campusIds
     *
     * @return UserDTO[]
     */
    public function findAllMatchingDTOsByCampusIds(array $campusIds)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from('IliosCoreBundle:User', 'u')
            ->where($qb->expr()->in('u.campusId', ':campusIds'));
        $qb->setParameter(':campusIds', $campusIds);

        return $this->createUserDTOs($qb->getQuery());
    }

    /**
     * Find all of the events for a user id between two dates
     * @param integer $id
     * @param \DateTime $from
     * @param \DateTime $to
     * @return UserEvent[]
     */
    public function findEventsForUser($id, \DateTime $from, \DateTime $to)
    {
        //These joins are DQL representations to go from a user to an offerings
        $joins = [
            ['g' => 'u.learnerGroups', 'o' => 'g.offerings'],
            ['g' => 'u.instructorGroups', 'o' => 'g.offerings'],
            ['o' => 'u.offerings'],
            ['o' => 'u.instructedOfferings'],
            ['dc' => 'u.directedCourses', 'dcs' => 'dc.sessions', 'o' => 'dcs.offerings'],
        ];

        $offeringEvents = [];
        //using each of the joins above create a query to get events
        foreach ($joins as $join) {
            $groupEvents = $this->getOfferingEventsFor($id, $from, $to, $join);
            $offeringEvents = array_merge($offeringEvents, $groupEvents);
        }

        $events = [];
        //extract unique offeringEvents by using the offering ID
        foreach ($offeringEvents as $userEvent) {
            if (!array_key_exists($userEvent->offering, $events)) {
                $events[$userEvent->offering] = $userEvent;
            }
        }

        //These joins are DQL representations to go from a user to an ILMSession
        $joins = [
            ['g' => 'u.learnerGroups', 'ilm' => 'g.ilmSessions'],
            ['g' => 'u.instructorGroups', 'ilm' => 'g.ilmSessions'],
            ['ilm' => 'u.learnerIlmSessions'],
            ['ilm' => 'u.instructorIlmSessions'],
            ['dc' => 'u.directedCourses', 'sess' => 'dc.sessions', 'ilm' => 'sess.ilmSession']
        ];

        $ilmEvents = [];
        //using each of the joins above create a query to get events
        foreach ($joins as $join) {
            $groupEvents = $this->getIlmSessionEventsFor($id, $from, $to, $join);
            $ilmEvents = array_merge($ilmEvents, $groupEvents);
        }

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
     * Get a list of users who do not have the former student role filtered by campus id
     * @param  array $campusIds
     * @return ArrayCollection
     */
    public function findUsersWhoAreNotFormerStudents(array $campusIds)
    {
        $qb = $this->_em->createQueryBuilder();
        $formerStudents = $qb->select('u.id')
            ->from('IliosCoreBundle:UserRole', 'r')
            ->leftJoin('r.users', 'u')
            ->where($qb->expr()->eq('r.title', ':fs_role_title'))
            ->setParameter('fs_role_title', 'Former Student')
            ->getQuery()
            ->getScalarResult();
        $formerStudentUserIds = array_map(function (array $arr) {
            return $arr['id'];
        }, $formerStudents);

        $qb2 = $this->_em->createQueryBuilder();
        $qb2->add('select', 'u')
            ->from('IliosCoreBundle:User', 'u')
            ->where('u.enabled=1')
            ->andWhere($qb->expr()->notIn('u.id', $formerStudentUserIds))
            ->addOrderBy('u.lastName', 'ASC')
            ->addOrderBy('u.firstName', 'ASC')
        ;
        if (!empty($campusIds)) {
            $qb2->andWhere($qb->expr()->in('u.campusId', $campusIds));
        }

        return new ArrayCollection($qb2->getQuery()->getResult());
    }

    /**
     * Get all the campus IDs for all users
     *
     * @param boolean $includeDisabled
     * @param boolean $includeSyncIgnore
     *
     * @return array
     */
    public function getAllCampusIds($includeDisabled, $includeSyncIgnore)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->add('select', 'u.campusId')->from('IliosCoreBundle:User', 'u');
        if (!$includeDisabled) {
            $qb->andWhere('u.enabled=1');
        }
        if (!$includeSyncIgnore) {
            $qb->andWhere('u.userSyncIgnore=0');
        }

        $campusIds = array_map(function (array $arr) {
            return $arr['campusId'];
        }, $qb->getQuery()->getScalarResult());

        return $campusIds;
    }

    /**
     * Reset examined flag for all users
     */
    public function resetExaminedFlagForAllUsers()
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->update('IliosCoreBundle:User', 'u')
            ->set('u.examined', $qb->expr()->literal(false));

        $qb->getQuery()->execute();
    }

    /**
     * Use the query builder and the $joins to get a set of
     * offering based user events
     *
     * @param integer $id
     * @param \DateTime $from
     * @param \DateTime $to
     * @param array $joins
     *
     * @return UserEvent[]
     */
    protected function getOfferingEventsFor(
        $id,
        \DateTime $from,
        \DateTime $to,
        array $joins
    ) {

        $qb = $this->_em->createQueryBuilder();
        $what = 'c.id as courseId, s.id AS sessionId, o.id, o.startDate, o.endDate, o.room, ' .
            'o.updatedAt AS offeringUpdatedAt, s.updatedAt AS sessionUpdatedAt, s.title, st.calendarColor, ' .
            's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
            's.attireRequired, s.equipmentRequired, s.supplemental, s.attendanceRequired, ' .
            'c.publishedAsTbd as coursePublishedAsTbd, c.published as coursePublished, c.title AS courseTitle, ' .
            'sd.description AS sessionDescription, st.title AS sessionTypeTitle, c.externalId AS courseExternalId';
        $qb->add('select', $what)->from('IliosCoreBundle:School', 'school');

        $qb->add('select', $what)->from('IliosCoreBundle:User', 'u');
        foreach ($joins as $key => $statement) {
            $qb->leftJoin($statement, $key);
        }
        $qb->leftJoin('o.session', 's');
        $qb->leftJoin('s.course', 'c');
        $qb->leftJoin('s.sessionType', 'st');
        $qb->leftJoin('s.sessionDescription', 'sd');


        $qb->andWhere($qb->expr()->eq('u.id', ':user_id'));
        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->between('o.startDate', ':date_from', ':date_to'),
            $qb->expr()->andX(
                $qb->expr()->lte('o.startDate', ':date_from'),
                $qb->expr()->gte('o.endDate', ':date_from')
            )
        ));
        $qb->setParameter('user_id', $id);

        $qb->setParameter('date_from', $from, DoctrineType::DATETIME);
        $qb->setParameter('date_to', $to, DoctrineType::DATETIME);

        $results = $qb->getQuery()->getArrayResult();
        return $this->createEventObjectsForOfferings($id, $results);
    }

    /**
     * Use the query builder and the $joins to get a set of
     * ILMSession based user events
     *
     * @param integer $id
     * @param \DateTime $from
     * @param \DateTime $to
     * @param array $joins
     * @return UserEvent[]
     */
    protected function getIlmSessionEventsFor($id, \DateTime $from, \DateTime $to, array $joins)
    {
        $qb = $this->_em->createQueryBuilder();
        $what = 'c.id as courseId, s.id AS sessionId, ilm.id, ilm.dueDate, ' .
            's.updatedAt, s.title, st.calendarColor, ' .
            's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
            's.attireRequired, s.equipmentRequired, s.supplemental, s.attendanceRequired, ' .
            'c.publishedAsTbd as coursePublishedAsTbd, c.published as coursePublished, c.title as courseTitle,' .
            'sd.description AS sessionDescription, st.title AS sessionTypeTitle, c.externalId AS courseExternalId';

        $qb->add('select', $what)->from('IliosCoreBundle:User', 'u');

        foreach ($joins as $key => $statement) {
            $qb->leftJoin($statement, $key);
        }
        $qb->leftJoin('ilm.session', 's');
        $qb->leftJoin('s.course', 'c');
        $qb->leftJoin('s.sessionType', 'st');
        $qb->leftJoin('s.sessionDescription', 'sd');

        $qb->where($qb->expr()->andX(
            $qb->expr()->eq('u.id', ':user_id'),
            $qb->expr()->between('ilm.dueDate', ':date_from', ':date_to')
        ));
        $qb->setParameter('user_id', $id);
        $qb->setParameter('date_from', $from, DoctrineType::DATETIME);
        $qb->setParameter('date_to', $to, DoctrineType::DATETIME);

        $results = $qb->getQuery()->getArrayResult();
        return $this->createEventObjectsForIlmSessions($id, $results);
    }


    /**
     * Convert offerings into UserEvent objects
     * @param integer $userId
     * @param array $results
     *
     * @return UserEvent[]
     */
    protected function createEventObjectsForOfferings($userId, array $results)
    {
        return array_map(function ($arr) use ($userId) {
            $event = new UserEvent;
            $event->user = $userId;
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
            $event->sessionTypeTitle = $arr['sessionTypeTitle'];
            $event->courseExternalId = $arr['courseExternalId'];
            $event->sessionDescription = $arr['sessionDescription'];
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
     * @param integer $userId
     * @param array $results
     * @return UserEvent[]
     */
    protected function createEventObjectsForIlmSessions($userId, array $results)
    {
        return array_map(function ($arr) use ($userId) {
            $event = new UserEvent();
            $event->user = $userId;
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
            $event->sessionTypeTitle = $arr['sessionTypeTitle'];
            $event->courseExternalId = $arr['courseExternalId'];
            $event->sessionDescription = $arr['sessionDescription'];
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
     * Retrieves a list of instructors associated with given offerings.
     *
     * @param array $ids A list of offering ids.
     * @return array A map of instructor lists, keyed off by offering ids.
     */
    protected function getInstructorsForOfferings(array $ids)
    {
        if (empty($ids)) {
            return [];
        }

        $qb = $this->_em->createQueryBuilder();
        $qb->select('o.id AS oId, u.id AS userId, u.firstName, u.lastName')
            ->from('IliosCoreBundle:User', 'u');
        $qb->leftJoin('u.instructedOfferings', 'o');
        $qb->where(
            $qb->expr()->in('o.id', ':offerings')
        );
        $qb->setParameter(':offerings', $ids);
        $instructedOfferings = $qb->getQuery()->getArrayResult();


        $qb = $this->_em->createQueryBuilder();
        $qb->select('o.id AS oId, u.id AS userId, u.firstName, u.lastName')
            ->from('IliosCoreBundle:User', 'u');
        $qb->leftJoin('u.instructorGroups', 'ig');
        $qb->leftJoin('ig.offerings', 'o');
        $qb->where(
            $qb->expr()->in('o.id', ':offerings')
        );
        $qb->setParameter(':offerings', $ids);
        $groupOfferings = $qb->getQuery()->getArrayResult();

        $results = array_merge($instructedOfferings, $groupOfferings);

        $offeringInstructors = [];
        foreach ($results as $result) {
            if (! array_key_exists($result['oId'], $offeringInstructors)) {
                $offeringInstructors[$result['oId']] = [];
            }
            $offeringInstructors[$result['oId']][$result['userId']] = $result['firstName'] . ' ' . $result['lastName'];
        }
        return $offeringInstructors;
    }

    /**
     * Retrieves a list of instructors associated with given ILM sessions.
     *
     * @param array $ids A list of ILM session ids.
     * @return array A map of instructor lists, keyed off by ILM sessions ids.
     */
    protected function getInstructorsForIlmSessions(array $ids)
    {
        if (empty($ids)) {
            return [];
        }

        $qb = $this->_em->createQueryBuilder();
        $qb->select('ilm.id AS ilmId, u.id AS userId, u.firstName, u.lastName')
            ->from('IliosCoreBundle:User', 'u');
        $qb->leftJoin('u.instructorIlmSessions', 'ilm');
        $qb->where(
            $qb->expr()->in('ilm.id', ':ilms')
        );
        $qb->setParameter(':ilms', $ids);
        $instructedIlms = $qb->getQuery()->getArrayResult();

        $qb = $this->_em->createQueryBuilder();
        $qb->select('ilm.id AS ilmId, u.id AS userId, u.firstName, u.lastName')
            ->from('IliosCoreBundle:User', 'u');
        $qb->leftJoin('u.instructorGroups', 'ig');
        $qb->leftJoin('ig.ilmSessions', 'ilm');
        $qb->where(
            $qb->expr()->in('ilm.id', ':ilms')
        );
        $qb->setParameter(':ilms', $ids);
        $groupIlms = $qb->getQuery()->getArrayResult();

        $results = array_merge($instructedIlms, $groupIlms);

        $ilmInstructors = [];
        foreach ($results as $result) {
            if (! array_key_exists($result['ilmId'], $ilmInstructors)) {
                $ilmInstructors[$result['ilmId']] = [];
            }
            $ilmInstructors[$result['ilmId']][$result['userId']] = $result['firstName'] . ' ' . $result['lastName'];
        }
        return $ilmInstructors;
    }

    /**
     * Adds instructors to a given list of events.
     * @param array $events A list of events
     * @return array The events list with instructors added.
     */
    public function addInstructorsToEvents(array $events)
    {
        $offeringIds = array_map(function ($event) {
            return $event->offering;
        }, array_filter($events, function ($event) {
            return $event->offering;
        }));

        $ilmIds = array_map(function ($event) {
            return $event->ilmSession;
        }, array_filter($events, function ($event) {
            return $event->ilmSession;
        }));

        // array-filtering throws off the array index.
        // set this right again.
        $events = array_values($events);

        $offeringInstructors = $this->getInstructorsForOfferings($offeringIds);
        $ilmInstructors = $this->getInstructorsForIlmSessions($ilmIds);

        for ($i = 0, $n = count($events); $i < $n; $i++) {
            if ($events[$i]->offering) { // event maps to offering
                if (array_key_exists($events[$i]->offering, $offeringInstructors)) {
                    $events[$i]->instructors = array_values($offeringInstructors[$events[$i]->offering]);
                }
            } elseif ($events[$i]->ilmSession) { // event maps to ILM session
                if (array_key_exists($events[$i]->ilmSession, $ilmInstructors)) {
                    $events[$i]->instructors = array_values($ilmInstructors[$events[$i]->ilmSession]);
                }
            }
        }
        return $events;
    }

    /**
     * Custom findBy so we can filter by related entities
     *
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
        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('u.' . $sort, $order);
            }
        }

        if (array_key_exists('instructedCourses', $criteria)) {
            $ids = is_array($criteria['instructedCourses'])
                ? $criteria['instructedCourses'] : [$criteria['instructedCourses']];
            $qb->leftJoin('u.instructedOfferings', 'ic_offering');
            $qb->leftJoin('u.instructorIlmSessions', 'ic_ilm');
            $qb->leftJoin('u.instructorGroups', 'ic_iGroup');
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
            $qb->leftJoin('u.instructedOfferings', 'is_offering');
            $qb->leftJoin('u.instructorIlmSessions', 'is_ilm');
            $qb->leftJoin('u.instructorGroups', 'is_iGroup');
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
            $qb->leftJoin('u.instructedOfferings', 'ilm_offering');
            $qb->leftJoin('u.instructorIlmSessions', 'ilm_ilm');
            $qb->leftJoin('u.instructorGroups', 'ilm_iGroup');
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
            $qb->leftJoin('u.instructedOfferings', 'it_offering');
            $qb->leftJoin('u.instructorIlmSessions', 'it_ilm');
            $qb->leftJoin('u.instructorGroups', 'it_iGroup');
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
            $qb->leftJoin('u.instructedOfferings', 'ist_offering');
            $qb->leftJoin('u.instructorIlmSessions', 'ist_ilm');
            $qb->leftJoin('u.instructorGroups', 'ist_iGroup');
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
            $qb->join('u.instructorGroups', 'ig_iGroup');
            $qb->andWhere($qb->expr()->in('ig_iGroup.id', ':instructorGroups'));
            $qb->setParameter(':instructorGroups', $ids);
        }

        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools'])
                ? $criteria['schools'] : [$criteria['schools']];
            $qb->join('u.school', 'sc_school');
            $qb->andWhere($qb->expr()->in('sc_school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
        }

        if (array_key_exists('roles', $criteria)) {
            $ids = is_array($criteria['roles'])
                ? $criteria['roles'] : [$criteria['roles']];
            $qb->join('u.roles', 'r_roles');
            $qb->andWhere($qb->expr()->in('r_roles.id', ':roles'));
            $qb->setParameter(':roles', $ids);
        }

        if (array_key_exists('cohorts', $criteria)) {
            $ids = is_array($criteria['cohorts'])
                ? $criteria['cohorts'] : [$criteria['cohorts']];
            if (in_array(null, $ids)) {
                $ids = array_diff($ids, [null]);
                $qb->andWhere('u.cohorts IS EMPTY');
            }
            if (count($ids)) {
                $qb->join('u.cohorts', 'c_cohorts');
                $qb->andWhere($qb->expr()->in('c_cohorts.id', ':cohorts'));
                $qb->setParameter(':cohorts', $ids);
            }
        }

        if (array_key_exists('learnerSessions', $criteria)) {
            $ids = is_array($criteria['learnerSessions'])
                ? $criteria['learnerSessions'] : [$criteria['learnerSessions']];

            $qb->leftJoin('u.offerings', 'ls_offering');
            $qb->leftJoin('u.learnerIlmSessions', 'ls_ilm');
            $qb->leftJoin('u.learnerGroups', 'ls_iGroup');
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

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("u.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
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
     * @param AbstractQuery $query
     * @return UserDTO[]
     */
    protected function createUserDTOs(AbstractQuery $query)
    {
        $userDTOs = [];
        foreach ($query->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $userDTOs[$arr['id']] = new UserDTO(
                $arr['id'],
                $arr['firstName'],
                $arr['lastName'],
                $arr['middleName'],
                $arr['phone'],
                $arr['email'],
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

        $userIds = array_keys($userDTOs);

        $qb = $this->_em->createQueryBuilder();
        $qb->select('u.id AS userId, c.id AS primaryCohortId, s.id AS schoolId, auser.id as authenticationId')
            ->from('IliosCoreBundle:User', 'u')
            ->join('u.school', 's')
            ->leftJoin('u.primaryCohort', 'c')
            ->leftJoin('u.authentication', 'a')
            ->leftJoin('a.user', 'auser')
            ->where($qb->expr()->in('u.id', ':userIds'))
            ->setParameter('userIds', $userIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $userDTOs[$arr['userId']]->primaryCohort = $arr['primaryCohortId'] ? $arr['primaryCohortId'] : null;
            $userDTOs[$arr['userId']]->school = $arr['schoolId'];
            $userDTOs[$arr['userId']]->authentication = $arr['authenticationId'] ? $arr['authenticationId'] : null;
        }

        $related = [
            'reminders',
            'directedCourses',
            'administeredCourses',
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
            'permissions',
            'learnerIlmSessions',
            'directedSchools',
            'administeredSchools',
            'administeredSessions',
            'directedPrograms',
            'administeredCurriculumInventoryReports',
        ];

        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder();
            $qb->select('r.id as relId, u.id AS userId')->from('IliosCoreBundle:User', 'u')
                ->join("u.{$rel}", 'r')
                ->where($qb->expr()->in('u.id', ':ids'))
                ->orderBy('relId')
                ->setParameter('ids', $userIds);

            foreach ($qb->getQuery()->getResult() as $arr) {
                $userDTOs[$arr['userId']]->{$rel}[] = $arr['relId'];
            }
        }

        return array_values($userDTOs);
    }

    /**
     * Find all of the assigned materials for a user
     * @param integer $id
     * @param UserMaterialFactory $factory
     * @param array $criteria
     *
     * @return UserMaterial[]
     */
    public function findMaterialsForUser($id, UserMaterialFactory $factory, $criteria)
    {
        $offIdQb = $this->_em->createQueryBuilder();
        $offIdQb->select('learnerOffering.id')->from('IliosCoreBundle:User', 'learnerU');
        $offIdQb->join('learnerU.offerings', 'learnerOffering');
        $offIdQb->andWhere($offIdQb->expr()->eq('learnerU.id', ':user_id'));

        $groupOfferingQb = $this->_em->createQueryBuilder();
        $groupOfferingQb->select('groupOffering.id')->from('IliosCoreBundle:User', 'groupU');
        $groupOfferingQb->leftJoin('groupU.learnerGroups', 'g');
        $groupOfferingQb->leftJoin('g.offerings', 'groupOffering');
        $groupOfferingQb->andWhere($groupOfferingQb->expr()->eq('groupU.id', ':user_id'));

        $qb = $this->_em->createQueryBuilder();
        $qb->select('s.id, o.startDate, o.id as offeringId');
        $qb->from('IliosCoreBundle:Offering', 'o');
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

        $ilmQb = $this->_em->createQueryBuilder();
        $ilmQb->select('learnerIlmSession.id')->from('IliosCoreBundle:User', 'learnerU');
        $ilmQb->join('learnerU.learnerIlmSessions', 'learnerIlmSession');
        $ilmQb->andWhere($ilmQb->expr()->eq('learnerU.id', ':user_id'));

        $groupIlmSessionQb = $this->_em->createQueryBuilder();
        $groupIlmSessionQb->select('groupIlmSession.id')->from('IliosCoreBundle:User', 'groupU');
        $groupIlmSessionQb->leftJoin('groupU.learnerGroups', 'g');
        $groupIlmSessionQb->leftJoin('g.ilmSessions', 'groupIlmSession');
        $groupIlmSessionQb->andWhere($groupIlmSessionQb->expr()->eq('groupU.id', ':user_id'));

        $qb = $this->_em->createQueryBuilder();
        $qb->select('s.id, ilm.dueDate, ilm.id AS ilmId');
        $qb->from('IliosCoreBundle:IlmSession', 'ilm');
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
        $offeringIds = array_map(function (array $arr) {
            return $arr['offeringId'];
        }, $offeringSessions);
        $ilmIds = array_map(function (array $arr) {
            return $arr['ilmId'];
        }, $ilmSessions);
        $offeringInstructors = $this->getInstructorsForOfferings($offeringIds);
        $ilmInstructors = $this->getInstructorsForIlmSessions($ilmIds);

        $sessions = [];
        foreach ($offeringSessions as $arr) {
            if (!array_key_exists($arr['id'], $sessions)) {
                $sessions[$arr['id']] = [
                    'firstOfferingDate' => $arr['startDate'],
                    'instructors' => []
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
                    'instructors' => []
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


        $sessionMaterials = $this->getLearningMaterialsForSessions($sessionIds);

        $sessionUserMaterials = array_map(function (array $arr) use ($factory, $sessions) {
            $arr['firstOfferingDate'] = $sessions[$arr['sessionId']]['firstOfferingDate'];
            $arr['instructors'] = $sessions[$arr['sessionId']]['instructors'];
            return $factory->create($arr);
        }, $sessionMaterials);

        $courseMaterials = $this->getLearningMaterialsForCourses($sessionIds);

        $courseUserMaterials = array_map(function (array $arr) use ($factory) {
            return $factory->create($arr);
        }, $courseMaterials);


        $userMaterials = array_merge($sessionUserMaterials, $courseUserMaterials);
        //sort materials by id for consistency
        usort($userMaterials, function (UserMaterial $a, UserMaterial $b) {
            return $a->id - $b->id;
        });

        return $userMaterials;
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
        $sessionIds = array_map(function (CalendarEvent $event) {
            return $event->sessionId;
        }, $events);

        $sessionIds = array_values(array_unique($sessionIds));

        $sessionMaterials = $this->getLearningMaterialsForSessions($sessionIds);

        $sessionUserMaterials = array_map(function (array $arr) use ($factory) {
            return $factory->create($arr);
        }, $sessionMaterials);

        $courseMaterials = $this->getLearningMaterialsForCourses($sessionIds);

        $courseUserMaterials = array_map(function (array $arr) use ($factory) {
            return $factory->create($arr);
        }, $courseMaterials);



        //sort materials by id for consistency
        $sortFn = function (UserMaterial $a, UserMaterial $b) {
            return $a->id - $b->id;
        };

        usort($sessionUserMaterials, $sortFn);
        usort($courseUserMaterials, $sortFn);

        // group materials by session or course
        $groupedSessionLms = [];
        $groupedCourseLms = [];
        for ($i = 0, $n = count($sessionUserMaterials); $i < $n; $i++) {
            $lm = $sessionUserMaterials[$i];
            $id = $lm->session;
            if (! array_key_exists($id, $groupedSessionLms)) {
                $groupedSessionLms[$id] = [];
            }
            $groupedSessionLms[$id][] = $lm;
        }
        for ($i = 0, $n = count($courseUserMaterials); $i < $n; $i++) {
            $lm = $courseUserMaterials[$i];
            $id = $lm->course;
            if (! array_key_exists($id, $groupedCourseLms)) {
                $groupedCourseLms[$id] = [];
            }
            $groupedCourseLms[$id][] = $lm;
        }

        for ($i =0, $n = count($events); $i < $n; $i++) {
            $event = $events[$i];
            $sessionId = $event->sessionId;
            $courseId = $event->courseId;
            $sessionLms = array_key_exists($sessionId, $groupedSessionLms) ? $groupedSessionLms[$sessionId] : [];
            $courseLms = array_key_exists($courseId, $groupedCourseLms) ? $groupedCourseLms[$courseId] : [];
            $lms = array_merge($sessionLms, $courseLms);
            $event->learningMaterials = $lms;
        }
        return $events;
    }

    /**
     * Get a set of learning materials based on session
     *
     * @param array $sessionIds
     *
     * @return array
     */
    protected function getLearningMaterialsForSessions(
        array $sessionIds
    ) {

        $qb = $this->_em->createQueryBuilder();
        $what = 's.title as sessionTitle, s.id as sessionId, ' .
            'c.id as courseId, c.title as courseTitle, ' .
            'slm.id as slmId, slm.position, slm.notes, slm.required, slm.publicNotes, slm.startDate, slm.endDate, ' .
            'lm.id, lm.title, lm.description, lm.originalAuthor, lm.token, ' .
            'lm.citation, lm.link, lm.filename, lm.filesize, lm.mimetype, lms.id AS status';
        $qb->select($what)->from('IliosCoreBundle:Session', 's');
        $qb->join('s.learningMaterials', 'slm');
        $qb->join('slm.learningMaterial', 'lm');
        $qb->join('lm.status', 'lms');
        $qb->join('s.course', 'c');

        $qb->andWhere($qb->expr()->in('s.id', ':sessions'));
        $qb->andWhere($qb->expr()->eq('s.published', 1));
        $qb->andWhere($qb->expr()->eq('s.publishedAsTbd', 0));
        $qb->andWhere($qb->expr()->eq('c.published', 1));
        $qb->andWhere($qb->expr()->eq('c.publishedAsTbd', 0));
        $qb->setParameter(':sessions', $sessionIds);
        $qb->distinct();

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * Get a set of course learning materials based on sessionIds
     *
     * @param array $sessionIds
     *
     * @return array
     */
    protected function getLearningMaterialsForCourses(
        array $sessionIds
    ) {

        $qb = $this->_em->createQueryBuilder();
        $what = 'c.title as courseTitle, c.id as courseId, c.startDate as firstOfferingDate, ' .
            'clm.id as clmId, clm.position, clm.notes, clm.required, clm.publicNotes, clm.startDate, clm.endDate, ' .
            'lm.id, lm.title, lm.description, lm.originalAuthor, lm.token, ' .
            'lm.citation, lm.link, lm.filename, lm.filesize, lm.mimetype, lms.id AS status';
        $qb->select($what)->from('IliosCoreBundle:Session', 's');
        $qb->join('s.course', 'c');
        $qb->join('c.learningMaterials', 'clm');
        $qb->join('clm.learningMaterial', 'lm');
        $qb->join('lm.status', 'lms');


        $qb->andWhere($qb->expr()->in('s.id', ':sessions'));
        $qb->andWhere($qb->expr()->eq('c.published', 1));
        $qb->andWhere($qb->expr()->eq('c.publishedAsTbd', 0));
        $qb->setParameter(':sessions', $sessionIds);
        $qb->distinct();

        return $qb->getQuery()->getArrayResult();
    }
}
