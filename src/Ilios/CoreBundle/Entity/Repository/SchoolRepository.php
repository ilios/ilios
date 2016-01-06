<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Types\Type as DoctrineType;
use Ilios\CoreBundle\Classes\SchoolEvent;
use Ilios\CoreBundle\Classes\UserEvent;

/**
 * Class SchoolRepository
 * @package Ilios\CoreBundle\Entity\Repository
 */
class SchoolRepository extends EntityRepository
{
    /**
     * Find all of the events for a user id between two dates
     * @param integer $id
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return UserEvent[]
     */
    public function findEventsForSchool($id, \DateTime $from, \DateTime $to)
    {
        //These joins are DQL representations to go from a user to an offerings
        $joins = [
            ['c' => 'school.courses', 'se' => 'c.sessions', 'o' => 'se.offerings'],
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
            ['c' => 'school.courses', 'se' => 'c.sessions', 'ilm' => 'se.ilmSession'],
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
      * Use the query builder and the $joins to get a set of
      * offering based school events
      *
      * @param integer $id
      * @param \DateTime $from
      * @param \DateTime $to
      * @param array $joins
      *
     * @return SchoolEvent[]
     */
    protected function getOfferingEventsFor(
        $id,
        \DateTime $from,
        \DateTime $to,
        array $joins
    ) {

        $qb = $this->_em->createQueryBuilder();
        $what = 'o.id, o.startDate, o.endDate, o.room, o.updatedAt, ' .
          's.title, s.publishedAsTbd, st.sessionTypeCssClass, pe.id as publishEventId, cpe.id as coursePublishEventId';
        $qb->add('select', $what)->from('IliosCoreBundle:School', 'school');
        foreach ($joins as $key => $statement) {
            $qb->leftJoin($statement, $key);
        }
        $qb->leftJoin('o.session', 's');
        $qb->leftJoin('c.publishEvent', 'cpe');
        $qb->leftJoin('s.sessionType', 'st');
        $qb->leftJoin('s.publishEvent', 'pe');
        
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
          's.updatedAt, s.title, s.publishedAsTbd, st.sessionTypeCssClass, pe.id as publishEventId' .
          'cpe.id as coursePublishEventId';
        $qb->add('select', $what)->from('IliosCoreBundle:School', 'school');
        foreach ($joins as $key => $statement) {
            $qb->leftJoin($statement, $key);
        }
        $qb->leftJoin('ilm.session', 's');
        $qb->leftJoin('c.publishEvent', 'cpe');
        $qb->leftJoin('s.sessionType', 'st');
        $qb->leftJoin('s.publishEvent', 'pe');

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
     * Convert offerings into UserEvent objects
     * @param integer $schoolId
     * @param array $results
     *
     * @return UserEvent[]
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
            $event->eventClass = $arr['sessionTypeCssClass'];
            $event->lastModified = $arr['updatedAt'];
            $event->isPublished = !empty($arr['publishEventId']) && !empty($arr['coursePublishEventId']);
            $event->isScheduled = $arr['publishedAsTbd'];

            return $event;
        }, $results);
    }

    
    /**
     * Convert IlmSessions into UserEvent objects
     * @param integer $schoolId
     * @param array $results
     *
     * @return UserEvent[]
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
            $event->eventClass = $arr['sessionTypeCssClass'];
            $event->lastModified = $arr['updatedAt'];
            $event->isPublished = !empty($arr['publishEventId']) && !empty($arr['coursePublishEventId']);
            $event->isScheduled = $arr['publishedAsTbd'];

            return $event;
        }, $results);
    }
}
