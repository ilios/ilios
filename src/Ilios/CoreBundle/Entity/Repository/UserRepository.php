<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Type as DoctrineType;
use Doctrine\DBAL\Query\QueryBuilder;
use Ilios\CoreBundle\Classes\UserEvent;

class UserRepository extends EntityRepository
{
    /**
     * Find by a string query
     * @param string $q
     * @param integer $orderBy
     * @param integer $limit
     * @param offset $offset
     */
    public function findByQ($q, $orderBy, $limit, $offset)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->add('select', 'u')->from('IliosCoreBundle:User', 'u');
        $terms = explode(' ', $q);
        $terms = array_filter($terms, 'strlen');
        if (empty($terms)) {
            return new ArrayCollection([]);
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

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('u.' . $sort, $order);
            }
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
     * @param integer $userId
     * @param \DateTime $start
     * @param \DateTime $end
     *
     * @return UserEvent[]|Collection
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
        //using each of the joins above create a query to get offerings
        foreach ($joins as $join) {
            $qb = $this->getOfferingEventsQueryBuilder($id, $from, $to, $join);
            $results = $qb->getQuery()->getArrayResult();
            $groupEvents = $this->createEventObjectsForOfferings($id, $results);
            $offeringEvents = array_merge($offeringEvents, $groupEvents);
        }
        
        $events = [];
        //extract unique offeringEvents by using the offering ID
        foreach ($offeringEvents as $userEvent) {
            if (!array_key_exists($userEvent->offering, $events)) {
                $events[$userEvent->offering] = $userEvent;
            }
        }
        
        //sort events by startDate for consistency
        usort($events, function ($a, $b) {
            return $a->startDate->getTimestamp() - $b->startDate->getTimestamp();
        });
        
        return $events;
    }
    
    /**
      * Use the query builder and the $joins to get a set of offerings
      * @param integer $userId
      * @param \DateTime $start
      * @param \DateTime $end
      * @param string $group
      *
     * @return QueryBuilder
     */
    protected function getOfferingEventsQueryBuilder(
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
            $qb->expr()->between('o.startDate', ':date_from', ':date_to'),
            $qb->expr()->eq('o.deleted', 0)
        ));
        $qb->setParameter('user_id', $id);

        $qb->setParameter('date_from', $from, DoctrineType::DATETIME);
        $qb->setParameter('date_to', $to, DoctrineType::DATETIME);

        return $qb;
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
}
