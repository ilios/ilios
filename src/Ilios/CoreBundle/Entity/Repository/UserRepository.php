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
use Ilios\CoreBundle\Entity\User;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Entity\DTO\UserDTO;
use Ilios\CoreBundle\Service\UserMaterialFactory;
use Ilios\CoreBundle\Traits\CalendarEventRepository;

/**
 * Class UserRepository
 */
class UserRepository extends EntityRepository implements DTORepositoryInterface
{
    use CalendarEventRepository;

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
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     * @param array $criteria
     * @return UserInterface[]
     */
    public function findByQ($q, $orderBy, $limit, $offset, array $criteria = array())
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->addSelect('u')->from('IliosCoreBundle:User', 'u');
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

        //cast calendar events into user events
        $userEvents = array_map(function (CalendarEvent $event) use ($id) {
            $userEvent = new UserEvent();
            $userEvent->user = $id;

            foreach (get_object_vars($event) as $key => $name) {
                $userEvent->$key = $name;
            }

            return $userEvent;
        }, $events);

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
        $qb2->addSelect('u')
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
        $qb->addSelect('u.campusId')->from('IliosCoreBundle:User', 'u');
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
     * @return CalendarEvent[]
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
        $qb->addSelect($what)->from('IliosCoreBundle:User', 'u');
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
     * @return CalendarEvent[]
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

        $qb->addSelect($what)->from('IliosCoreBundle:User', 'u');

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
     * Adds instructors to a given list of events.
     * @param UserEvent[] $events A list of events
     *
     * @return UserEvent[] The events list with instructors added.
     */
    public function addInstructorsToEvents(array $events)
    {
        return $this->attachInstructorsToEvents($events, $this->_em);
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
        $offeringInstructors = $this->getInstructorsForOfferings($offeringIds, $this->_em);
        $ilmInstructors = $this->getInstructorsForIlmSessions($ilmIds, $this->_em);

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


        $sessionMaterials = $this->getSessionLearningMaterialsForPublishedSessions($sessionIds, $this->_em);

        $sessionUserMaterials = array_map(function (array $arr) use ($factory, $sessions) {
            $arr['firstOfferingDate'] = $sessions[$arr['sessionId']]['firstOfferingDate'];
            $arr['instructors'] = $sessions[$arr['sessionId']]['instructors'];
            return $factory->create($arr);
        }, $sessionMaterials);

        $courseMaterials = $this->getCourseLearningMaterialsForPublishedSessions($sessionIds, $this->_em);

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
        return $this->attachMaterialsToEvents($events, $factory, $this->_em);
    }

    /**
     * Finds and adds course- and session-objectives and their competencies to a given list of calendar events.
     *
     * @param CalendarEvent[] $events
     * @return CalendarEvent[]
     */
    public function addObjectivesAndCompetenciesToEvents(array $events)
    {
        return $this->attachObjectivesAndCompetenciesToEvents($events, $this->_em);
    }

    /**
     * Returns a list of ids of schools directed by the given user.
     * @param $userId
     * @return array
     */
    public function getDirectedSchoolIds($userId): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('school.id')->distinct()->from(User::class, 'u');
        $qb->join('u.directedSchools', 'school');
        $qb->andWhere($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        return array_column($qb->getQuery()->getArrayResult(), 'id');
    }

    /**
     * Returns a list of ids of schools administered by the given user.
     * @param $userId
     * @return array
     */
    public function getAdministeredSchoolIds($userId): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('school.id')->distinct()->from(User::class, 'u');
        $qb->join('u.administeredSchools', 'school');
        $qb->andWhere($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        return array_column($qb->getQuery()->getArrayResult(), 'id');
    }

    /**
     * Returns an assoc. array of ids os courses directed by the given user,
     * and the ids of schools owning these directed courses.
     * @param $userId
     * @return array
     */
    public function getDirectedCourseAndSchoolIds($userId): array
    {
        $rhett['schoolIds'] = [];
        $rhett['courseIds'] = [];

        $qb = $this->_em->createQueryBuilder();
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
     * @param $userId
     * @return array
     */
    public function getAdministeredCourseAndSchoolIds($userId)
    {
        $rhett['schoolIds'] = [];
        $rhett['courseIds'] = [];

        $qb = $this->_em->createQueryBuilder();
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
     * @param $userId
     * @return array
     */
    public function getAdministeredCurriculumInventoryReportAndSchoolIds($userId): array
    {
        $rhett['reportIds'] = [];
        $rhett['schoolIds'] = [];

        $qb = $this->_em->createQueryBuilder();
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
     * @param $userId
     * @return array
     */
    public function getAdministeredSessionCourseAndSchoolIds($userId): array
    {
        $rhett['schoolIds'] = [];
        $rhett['courseIds'] = [];
        $rhett['sessionIds'] = [];

        $qb = $this->_em->createQueryBuilder();
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

    /**
     * Returns a list of ids of schools which own learner groups instructed by the given user.
     * @param $userId
     * @return array
     */
    public function getInstructedLearnerGroupSchoolIds($userId): array
    {
        $qb = $this->_em->createQueryBuilder();
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
     * @param $userId
     * @return array
     */
    public function getLearnerGroupIds($userId): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('learnerGroups.id')->distinct()->from(User::class, 'u');
        $qb->join('u.learnerGroups', 'learnerGroups');
        $qb->where($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        return array_column($qb->getQuery()->getArrayResult(), 'id');
    }

    /**
     * Returns a list of ids of instructor groups that the given user is a member of.
     * @param $userId
     * @return array
     */
    public function getInstructorGroupIds($userId): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('instructorGroups.id')->distinct()->from(User::class, 'u');
        $qb->join('u.instructorGroups', 'instructorGroups');
        $qb->where($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        return array_column($qb->getQuery()->getArrayResult(), 'id');
    }

    /**
     * Returns a list of ids of schools owning instructor groups that the given user is part of.
     * @param $userId
     * @return array
     */
    public function getInstructorGroupSchoolIds($userId): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('school.id')->distinct()->from(User::class, 'u');
        $qb->join('u.instructorGroups', 'instructorGroup');
        $qb->join('instructorGroup.school', 'school');
        $qb->andWhere($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        return array_column($qb->getQuery()->getArrayResult(), 'id');
    }

    /**
     * Returns an assoc. array of ids of sessions instructed by the given user,
     * and the ids of schools and courses owning these instructed sessions.
     * @param $userId
     * @return array
     */
    public function getInstructedSessionCourseAndSchoolIds($userId): array
    {
        $rhett['schoolIds'] = [];
        $rhett['courseIds'] = [];
        $rhett['sessionIds'] = [];

        $qb = $this->_em->createQueryBuilder();
        $qb->select('session.id as sessionId, course.id as courseId, school.id as schoolId')
            ->distinct()
            ->from(User::class, 'u');
        $qb->join('u.instructorGroups', 'instructorGroup');
        $qb->join('instructorGroup.offerings', 'offerings');
        $qb->join('offerings.session', 'session');
        $qb->join('session.course', 'course');
        $qb->join('course.school', 'school');
        $qb->andWhere($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        foreach ($qb->getQuery()->getArrayResult() as $arr) {
            $rhett['schoolIds'][] = $arr['schoolId'];
            $rhett['courseIds'][] = $arr['courseId'];
            $rhett['sessionIds'][] = $arr['sessionId'];
        }

        $qb = $this->_em->createQueryBuilder();
        $qb->select('session.id as sessionId, course.id as courseId, school.id as schoolId')
            ->distinct()
            ->from(User::class, 'u');
        $qb->join('u.instructorGroups', 'instructorGroup');
        $qb->join('instructorGroup.ilmSessions', 'ilmSessions');
        $qb->join('ilmSessions.session', 'session');
        $qb->join('session.course', 'course');
        $qb->join('course.school', 'school');
        $qb->andWhere($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        foreach ($qb->getQuery()->getArrayResult() as $arr) {
            $rhett['schoolIds'][] = $arr['schoolId'];
            $rhett['courseIds'][] = $arr['courseId'];
            $rhett['sessionIds'][] = $arr['sessionId'];
        }

        $qb = $this->_em->createQueryBuilder();
        $qb->select('session.id as sessionId, course.id as courseId, school.id as schoolId')
            ->distinct()
            ->from(User::class, 'u');
        $qb->join('u.instructedOfferings', 'offerings');
        $qb->join('offerings.session', 'session');
        $qb->join('session.course', 'course');
        $qb->join('course.school', 'school');
        $qb->andWhere($qb->expr()->eq('u.id', ':userId'));
        $qb->setParameter(':userId', $userId);

        foreach ($qb->getQuery()->getArrayResult() as $arr) {
            $rhett['schoolIds'][] = $arr['schoolId'];
            $rhett['courseIds'][] = $arr['courseId'];
            $rhett['sessionIds'][] = $arr['sessionId'];
        }

        $qb = $this->_em->createQueryBuilder();
        $qb->select('session.id as sessionId, course.id as courseId, school.id as schoolId')
            ->distinct()
            ->from(User::class, 'u');
        $qb->join('u.instructorIlmSessions', 'ilmSession');
        $qb->join('ilmSession.session', 'session');
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

    /**
     * Returns an assoc. array of ids of programs directed by the given user,
     * and the ids of schools owning these directed programs.
     * @param $userId
     * @return array
     */
    public function getDirectedProgramAndSchoolIds($userId): array
    {
        $rhett['programIds'] = [];
        $rhett['schoolIds'] = [];

        $qb = $this->_em->createQueryBuilder();
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
     * @param $userId
     * @return array
     */
    public function getDirectedProgramYearProgramAndSchoolIds($userId): array
    {
        $rhett['programYearIds'] = [];
        $rhett['programIds'] = [];
        $rhett['schoolIds'] = [];

        $qb = $this->_em->createQueryBuilder();
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
     *
     * @param array $map
     * @return array
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
}
