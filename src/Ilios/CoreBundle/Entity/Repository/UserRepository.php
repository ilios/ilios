<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\DBAL\Types\Type as DoctrineType;
use Ilios\CoreBundle\Classes\UserEvent;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Entity\DTO\UserDTO;

/**
 * Class UserRepository
 * @package Ilios\CoreBundle\Entity\Repository
 */
class UserRepository extends EntityRepository
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
        $qb = $this->_em->createQueryBuilder()->select('u')->from('IliosCoreBundle:User', 'u');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        $userDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $userDTOs[$arr['id']] = new UserDTO(
                $arr['id'],
                $arr['firstName'],
                $arr['lastName'],
                $arr['middleName'],
                $arr['phone'],
                $arr['email'],
                $arr['enabled'],
                $arr['campusId'],
                $arr['otherId'],
                $arr['userSyncIgnore'],
                $arr['icsFeedKey']
            );
        }

        $userIds = array_keys($userDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select('u.id AS userId, c.id AS primaryCohortId, s.id AS schoolId')
            ->from('IliosCoreBundle:User', 'u')
            ->join('u.school', 's')
            ->leftJoin('u.primaryCohort', 'c')
            ->where($qb->expr()->in('u.id', ':userIds'))
            ->setParameter('userIds', $userIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $userDTOs[$arr['userId']]->primaryCohort = $arr['primaryCohortId'] ? $arr['primaryCohortId'] : null;
            $userDTOs[$arr['userId']]->school = $arr['schoolId'];
        }

        $related = [
            'reminders',
            'directedCourses',
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
            'learnerIlmSessions'
        ];

        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id as relId, u.id AS userId')->from('IliosCoreBundle:User', 'u')
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
        //sort events by startDate for consistency
        usort($events, function ($a, $b) {
            return $a->startDate->getTimestamp() - $b->startDate->getTimestamp();
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
        $what = 'o.id, o.startDate, o.endDate, o.room, o.updatedAt AS offeringUpdatedAt, ' .
            's.updatedAt AS sessionUpdatedAt, s.title, st.sessionTypeCssClass, ' .
            's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
            'c.publishedAsTbd as coursePublishedAsTbd, c.published as coursePublished, c.title as courseTitle';
        $qb->add('select', $what)->from('IliosCoreBundle:School', 'school');

        $qb->add('select', $what)->from('IliosCoreBundle:User', 'u');
        foreach ($joins as $key => $statement) {
            $qb->leftJoin($statement, $key);
        }
        $qb->leftJoin('o.session', 's');
        $qb->leftJoin('s.course', 'c');
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
        $what = 'ilm.id, ilm.dueDate, ' .
            's.updatedAt, s.title, st.sessionTypeCssClass, ' .
            's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
            'c.publishedAsTbd as coursePublishedAsTbd, c.published as coursePublished, c.title as courseTitle';
        $qb->add('select', $what)->from('IliosCoreBundle:User', 'u');
        foreach ($joins as $key => $statement) {
            $qb->leftJoin($statement, $key);
        }
        $qb->leftJoin('ilm.session', 's');
        $qb->leftJoin('s.course', 'c');
        $qb->leftJoin('s.sessionType', 'st');

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
            $event->eventClass = $arr['sessionTypeCssClass'];
            $event->lastModified = max($arr['offeringUpdatedAt'], $arr['sessionUpdatedAt']);
            $event->isPublished = $arr['sessionPublished']  && $arr['coursePublished'];
            $event->isScheduled = $arr['sessionPublishedAsTbd'] || $arr['coursePublishedAsTbd'];
            $event->courseTitle = $arr['courseTitle'];
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
            $event->eventClass = $arr['sessionTypeCssClass'];
            $event->lastModified = $arr['updatedAt'];
            $event->isPublished = $arr['sessionPublished']  && $arr['coursePublished'];
            $event->isScheduled = $arr['sessionPublishedAsTbd'] || $arr['coursePublishedAsTbd'];
            $event->courseTitle = $arr['courseTitle'];
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
        $qb->select('o.id oId, o2.id AS oId2, u.id AS userId, u.firstName, u.lastName')
            ->from('IliosCoreBundle:User', 'u');
        $qb->leftJoin('u.instructedOfferings', 'o');
        $qb->leftJoin('u.instructorGroups', 'ig');
        $qb->leftJoin('ig.offerings', 'o2');
        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->in('o.id', ':offerings'),
                $qb->expr()->in('o2.id', ':offerings')
            )
        );
        $qb->setParameter(':offerings', $ids);
        $results = $qb->getQuery()->getArrayResult();

        $offeringInstructors = [];
        foreach ($results as $result) {
            if ($result['oId']) {
                if (! array_key_exists($result['oId'], $offeringInstructors)) {
                    $offeringInstructors[$result['oId']] = [];
                }
                $offeringInstructors[$result['oId']][$result['userId']]
                    = $result['firstName'] . ' ' . $result['lastName'];
            }
            if ($result['oId2']) {
                if (! array_key_exists($result['oId2'], $offeringInstructors)) {
                    $offeringInstructors[$result['oId2']] = [];
                }
                $offeringInstructors[$result['oId2']][$result['userId']]
                    = $result['firstName'] . ' ' . $result['lastName'];
            }
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
        $qb->select('ilm.id ilmId, ilm2.id AS ilmId2, u.id AS userId, u.firstName, u.lastName')
            ->from('IliosCoreBundle:User', 'u');
        $qb->leftJoin('u.instructorIlmSessions', 'ilm');
        $qb->leftJoin('u.instructorGroups', 'ig');
        $qb->leftJoin('ig.ilmSessions', 'ilm2');
        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->in('ilm.id', ':ilms'),
                $qb->expr()->in('ilm2.id', ':ilms')
            )
        );
        $qb->setParameter(':ilms', $ids);
        $results = $qb->getQuery()->getArrayResult();

        $ilmInstructors = [];
        foreach ($results as $result) {
            if ($result['ilmId']) {
                if (! array_key_exists($result['ilmId'], $ilmInstructors)) {
                    $ilmInstructors[$result['ilmId']] = [];
                }
                $ilmInstructors[$result['ilmId']][$result['userId']] = $result['firstName'] . ' ' . $result['lastName'];
            }
            if ($result['ilmId2']) {
                if (! array_key_exists($result['ilmId2'], $ilmInstructors)) {
                    $ilmInstructors[$result['ilmId2']] = [];
                }
                $ilmInstructors[$result['ilmId2']][$result['userId']]
                    = $result['firstName'] . ' ' . $result['lastName'];
            }
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
                $qb->expr()->in('is_session.id', ':sessions'),
                $qb->expr()->in('is_session.id', ':sessions'),
                $qb->expr()->in('is_session.id', ':sessions')
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

        if (array_key_exists('instructedTopics', $criteria)) {
            $ids = is_array($criteria['instructedTopics'])
                ? $criteria['instructedTopics'] : [$criteria['instructedTopics']];
            $qb->leftJoin('u.instructedOfferings', 'it_offering');
            $qb->leftJoin('u.instructorIlmSessions', 'it_ilm');
            $qb->leftJoin('u.instructorGroups', 'it_iGroup');
            $qb->leftJoin('it_iGroup.offerings', 'it_offering2');
            $qb->leftJoin('it_iGroup.ilmSessions', 'it_ilm2');
            $qb->leftJoin('it_offering.session', 'it_session');
            $qb->leftJoin('it_ilm.session', 'it_session2');
            $qb->leftJoin('it_offering2.session', 'it_session3');
            $qb->leftJoin('it_ilm2.session', 'it_session4');
            $qb->leftJoin('it_session.topics', 'it_topic');
            $qb->leftJoin('it_session2.topics', 'it_topic2');
            $qb->leftJoin('it_session3.topics', 'it_topic3');
            $qb->leftJoin('it_session4.topics', 'it_topic4');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('it_topic.id', ':topics'),
                $qb->expr()->in('it_topic2.id', ':topics'),
                $qb->expr()->in('it_topic3.id', ':topics'),
                $qb->expr()->in('it_topic4.id', ':topics')
            ));
            $qb->setParameter(':topics', $ids);
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

        //cleanup all the possible relationship filters
        unset($criteria['schools']);
        unset($criteria['instructedCourses']);
        unset($criteria['instructedSessions']);
        unset($criteria['instructedLearningMaterials']);
        unset($criteria['instructedTopics']);
        unset($criteria['instructedSessionTypes']);
        unset($criteria['instructorGroups']);

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
}
