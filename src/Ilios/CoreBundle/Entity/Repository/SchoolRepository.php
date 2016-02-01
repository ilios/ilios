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
        //sort events by startDate for consistency
        usort($events, function ($a, $b) {
            return $a->startDate->getTimestamp() - $b->startDate->getTimestamp();
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
        $what = 'o.id, o.startDate, o.endDate, o.room, o.updatedAt, o.updatedAt AS offeringUpdatedAt, ' .
          's.updatedAt AS sessionUpdatedAt, s.title, st.sessionTypeCssClass, ' .
          's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
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
     * @return UserEvent[]
     */
    protected function getIlmSessionEventsFor(
        $id,
        \DateTime $from,
        \DateTime $to
    ) {

        $qb = $this->_em->createQueryBuilder();

        $what = 'ilm.id, ilm.dueDate, ' .
            's.updatedAt, s.title, st.sessionTypeCssClass, ' .
            's.publishedAsTbd as sessionPublishedAsTbd, s.published as sessionPublished, ' .
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
            $event->lastModified = max($arr['offeringUpdatedAt'], $arr['sessionUpdatedAt']);
            $event->isPublished = $arr['sessionPublished']  && $arr['coursePublished'];
            $event->isScheduled = $arr['sessionPublishedAsTbd'] || $arr['coursePublishedAsTbd'];
            $event->courseTitle = $arr['courseTitle'];
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
            $event->isPublished = $arr['sessionPublished']  && $arr['coursePublished'];
            $event->isScheduled = $arr['sessionPublishedAsTbd'] || $arr['coursePublishedAsTbd'];
            $event->courseTitle = $arr['courseTitle'];
            return $event;
        }, $results);
    }
}
