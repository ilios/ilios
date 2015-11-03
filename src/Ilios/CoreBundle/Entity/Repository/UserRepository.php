<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Type as DoctrineType;
use Ilios\CoreBundle\Classes\UserEvent;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class UserRepository
 * @package Ilios\CoreBundle\Entity\Repository
 */
class UserRepository extends EntityRepository
{
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
                $qb->expr()->like('u.email', "?{$key}")
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
     * Find all of the events for a user id between two dates
     * @param integer $id
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return UserEvent[]
     */
    public function findEventsForUser(
        $id,
        \DateTime $from,
        \DateTime $to
    ) {
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
        $what = 'o.id, o.startDate, o.endDate, o.room, o.updatedAt, ' .
          's.title, s.publishedAsTbd, st.sessionTypeCssClass, pe.id as publishEventId';
        $qb->add('select', $what)->from('IliosCoreBundle:User', 'u');
        foreach ($joins as $key => $statement) {
            $qb->leftJoin($statement, $key);
        }
        $qb->leftJoin('o.session', 's');
        $qb->leftJoin('s.sessionType', 'st');
        $qb->leftJoin('s.publishEvent', 'pe');

        $qb->where($qb->expr()->andX(
            $qb->expr()->eq('u.id', ':user_id'),
            $qb->expr()->between('o.startDate', ':date_from', ':date_to')
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
      *
     * @return UserEvent[]
     */
    protected function getIlmSessionEventsFor(
        $id,
        \DateTime $from,
        \DateTime $to,
        array $joins
    ) {

        $qb = $this->_em->createQueryBuilder();
        $what = 'ilm.id, ilm.dueDate, ' .
          's.updatedAt, s.title, s.publishedAsTbd, st.sessionTypeCssClass, pe.id as publishEventId';
        $qb->add('select', $what)->from('IliosCoreBundle:User', 'u');
        foreach ($joins as $key => $statement) {
            $qb->leftJoin($statement, $key);
        }
        $qb->leftJoin('ilm.session', 's');
        $qb->leftJoin('s.sessionType', 'st');
        $qb->leftJoin('s.publishEvent', 'pe');

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
            $event->lastModified = $arr['updatedAt'];
            $event->isPublished = !empty($arr['publishEventId']);
            $event->isScheduled = $arr['publishedAsTbd'];

            return $event;
        }, $results);
    }

    
    /**
     * Convert IlmSessions into UserEvent objects
     * @param integer $userId
     * @param array $results
     *
     * @return UserEvent[]
     */
    protected function createEventObjectsForIlmSessions($userId, array $results)
    {
        return array_map(function ($arr) use ($userId) {
            $event = new UserEvent;
            $event->user = $userId;
            $event->name = $arr['title'];
            $event->startDate = $arr['dueDate'];
            $event->ilmSession = $arr['id'];
            $event->eventClass = $arr['sessionTypeCssClass'];
            $event->lastModified = $arr['updatedAt'];
            $event->isPublished = !empty($arr['publishEventId']);
            $event->isScheduled = $arr['publishedAsTbd'];

            return $event;
        }, $results);
    }
}
