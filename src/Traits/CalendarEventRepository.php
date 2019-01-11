<?php

namespace App\Traits;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use App\Classes\CalendarEvent;
use App\Classes\UserMaterial;
use App\Service\UserMaterialFactory;

/**
 * Class CalendarEventRepository
 *
 * Loads CalendarEvents
 */
trait CalendarEventRepository
{
    /**
     * Convert offerings into CalendarEvent() objects
     * @param array $results
     *
     * @return CalendarEvent[]
     */
    protected function createEventObjectsForOfferings(array $results)
    {
        return array_map(function ($arr) {
            return $this->createEventObjectForOffering($arr);
        }, $results);
    }

    /**
     * @param array $arr
     * @return CalendarEvent
     */
    protected function createEventObjectForOffering(array $arr)
    {
        $event = new CalendarEvent();
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
        $event->instructionalNotes = $arr['instructionalNotes'];
        $event->session = $arr['sessionId'];
        $event->courseId = $arr['courseId'];
        $event->attireRequired = $arr['attireRequired'];
        $event->equipmentRequired = $arr['equipmentRequired'];
        $event->supplemental = $arr['supplemental'];
        $event->attendanceRequired = $arr['attendanceRequired'];
        return $event;
    }

    /**
     * Convert IlmSessions into CalendarEvent() objects
     * @param integer $userId
     * @param array $results
     * @return CalendarEvent[]
     */
    protected function createEventObjectsForIlmSessions($userId, array $results)
    {
        return array_map(function ($arr) use ($userId) {
            return $this->createEventObjectForIlmSession($userId, $arr);
        }, $results);
    }

    /**
     * @param $userId
     * @param array $arr
     * @return CalendarEvent
     */
    protected function createEventObjectForIlmSession($userId, array $arr)
    {
        $event = new CalendarEvent();
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
        $event->session = $arr['sessionId'];
        $event->courseId = $arr['courseId'];
        $event->attireRequired = $arr['attireRequired'];
        $event->equipmentRequired = $arr['equipmentRequired'];
        $event->supplemental = $arr['supplemental'];
        $event->attendanceRequired = $arr['attendanceRequired'];
        return $event;
    }

    /**
     * Retrieves a list of instructors associated with given offerings.
     *
     * @param array $ids A list of offering ids.
     * @return array A map of instructor lists, keyed off by offering ids.
     */
    protected function getInstructorsForOfferings(array $ids, EntityManager $em)
    {
        if (empty($ids)) {
            return [];
        }

        $qb = $em->createQueryBuilder();
        $qb->select('o.id AS oId, u.id AS userId, u.firstName, u.lastName')
            ->from('App\Entity\User', 'u');
        $qb->leftJoin('u.instructedOfferings', 'o');
        $qb->where(
            $qb->expr()->in('o.id', ':offerings')
        );
        $qb->setParameter(':offerings', $ids);
        $instructedOfferings = $qb->getQuery()->getArrayResult();


        $qb = $em->createQueryBuilder();
        $qb->select('o.id AS oId, u.id AS userId, u.firstName, u.lastName')
            ->from('App\Entity\User', 'u');
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
     * @param EntityManager $em
     * @return array A map of instructor lists, keyed off by ILM sessions ids.
     */
    protected function getInstructorsForIlmSessions(array $ids, EntityManager $em)
    {
        if (empty($ids)) {
            return [];
        }

        $qb = $em->createQueryBuilder();
        $qb->select('ilm.id AS ilmId, u.id AS userId, u.firstName, u.lastName')
            ->from('App\Entity\User', 'u');
        $qb->leftJoin('u.instructorIlmSessions', 'ilm');
        $qb->where(
            $qb->expr()->in('ilm.id', ':ilms')
        );
        $qb->setParameter(':ilms', $ids);
        $instructedIlms = $qb->getQuery()->getArrayResult();

        $qb = $em->createQueryBuilder();
        $qb->select('ilm.id AS ilmId, u.id AS userId, u.firstName, u.lastName')
            ->from('App\Entity\User', 'u');
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
     * @param EntityManager $em
     * @return array The events list with instructors added.
     */
    public function attachInstructorsToEvents(array $events, EntityManager $em)
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

        $offeringInstructors = $this->getInstructorsForOfferings($offeringIds, $em);
        $ilmInstructors = $this->getInstructorsForIlmSessions($ilmIds, $em);

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
     * Adds course- and session-objectives and their competencies to a given list of events.
     * @param array $events A list of events
     * @param EntityManager $em
     * @return array The events list with objectives and competencies added.
     */
    public function attachSessionDataToEvents(array $events, EntityManager $em)
    {
        $sessionIds = array_unique(array_column($events, 'session'));
        $courseIds = array_unique(array_column($events, 'courseId'));
        $competencyIds = [];

        $qb = $em->createQueryBuilder();
        $qb->select('s.id AS session_id, so.id, so.title, so.position, cm.id AS competency_id')
            ->distinct()
            ->from('App\Entity\Session', 's')
            ->join('s.objectives', 'so')
            ->leftJoin('so.parents', 'co')
            ->leftJoin('co.parents', 'po')
            ->leftJoin('po.competency', 'cm')
            ->where($qb->expr()->in('s.id', ':ids'))
            ->setParameter(':ids', $sessionIds);

        $results = $qb->getQuery()->getArrayResult();

        $sessionObjectives = [];

        foreach ($results as $result) {
            $sessionId = $result['session_id'];
            $objectiveId = $result['id'];
            $competencyId = $result['competency_id'];

            if (! array_key_exists($sessionId, $sessionObjectives)) {
                $sessionObjectives[$sessionId] = [];
            }
            if (! array_key_exists($objectiveId, $sessionObjectives[$sessionId])) {
                $sessionObjectives[$sessionId][$objectiveId] = [
                    'id' => $objectiveId,
                    'title' => $result['title'],
                    'position' => $result['position'],
                    'competencies' => [],
                ];
            }

            if (! empty($competencyId)
                && ! in_array($competencyId, $sessionObjectives[$sessionId][$objectiveId]['competencies'])
            ) {
                $sessionObjectives[$sessionId][$objectiveId]['competencies'][] = $competencyId;
            }

            $competencyIds[] = $competencyId;
        }

        $qb = $em->createQueryBuilder();
        $qb->select('c.id AS course_id, co.id, co.title, co.position, cm.id AS competency_id')
            ->distinct()
            ->from('App\Entity\Course', 'c')
            ->join('c.objectives', 'co')
            ->leftJoin('co.parents', 'po')
            ->leftJoin('po.competency', 'cm')
            ->where($qb->expr()->in('c.id', ':ids'))
            ->setParameter(':ids', $courseIds);

        $results = $qb->getQuery()->getArrayResult();

        $courseObjectives =  [];

        foreach ($results as $result) {
            $courseId = $result['course_id'];
            $objectiveId = $result['id'];
            $competencyId = $result['competency_id'];

            if (! array_key_exists($courseId, $courseObjectives)) {
                $courseObjectives[$courseId] = [];
            }
            if (! array_key_exists($objectiveId, $courseObjectives[$courseId])) {
                $courseObjectives[$courseId][$objectiveId] = [
                    'id' => $objectiveId,
                    'title' => $result['title'],
                    'position' => $result['position'],
                    'competencies' => [],
                ];
            }

            if (! empty($competencyId)
                && ! in_array($competencyId, $courseObjectives[$courseId][$objectiveId]['competencies'])
            ) {
                $courseObjectives[$courseId][$objectiveId]['competencies'][] = $competencyId;
            }

            $competencyIds[] = $competencyId;
        }

        $competencyIds = array_values(array_unique(array_filter($competencyIds)));

        $qb = $em->createQueryBuilder();
        $qb->select('cm.id, cm.title, cm2.id AS parent_id, cm2.title AS parent_title')
            ->distinct()
            ->from('App\Entity\Competency', 'cm')
            ->leftJoin('cm.parent', 'cm2')
            ->where($qb->expr()->in('cm.id', ':ids'))
            ->setParameter(':ids', $competencyIds);

        $results = $qb->getQuery()->getArrayResult();
        $competencies = [];
        foreach ($results as $result) {
            if (! array_key_exists($result['id'], $competencies)) {
                $competencies[$result['id']] = [
                    'id' => $result['id'],
                    'title' => $result['title'],
                    'parent' => $result['parent_id'],
                ];
            }
            if (! empty($result['parent_id']) && ! array_key_exists($result['parent_id'], $competencies)) {
                $competencies[$result['parent_id']] = [
                    'id' => $result['parent_id'],
                    'title' => $result['parent_title'],
                    'parent' => null,
                ];
            }
        }

        for ($i = 0, $n = count($events); $i < $n; $i++) {
            /** @var CalendarEvent $event */
            $event = $events[$i];
            if (array_key_exists($event->session, $sessionObjectives)) {
                $event->sessionObjectives = array_values($sessionObjectives[$event->session]);
            }
            if (array_key_exists($event->courseId, $courseObjectives)) {
                $event->courseObjectives = array_values($courseObjectives[$event->courseId]);
            }

            $listsOfCompetencyIds = array_merge(
                array_column($event->sessionObjectives, 'competencies'),
                array_column($event->courseObjectives, 'competencies')
            );
            $competencyIds = [];
            // flatted out lists of competency ids
            // @link https://stackoverflow.com/a/1320156
            array_walk_recursive($listsOfCompetencyIds, function ($a) use (&$competencyIds) {
                $competencyIds[] = $a;
            });
            // filter out null values and de-dupe list
            $competencyIds = array_values(array_unique(array_filter($competencyIds)));

            $tmp = [];
            foreach ($competencyIds as $id) {
                $competency = $competencies[$id];
                $tmp[$id] = $competency;
                if (! empty($competency['parent'])) {
                    $tmp[$competency['parent']] = $competencies[$competency['parent']];
                }
            }
            $event->competencies = array_values($tmp);
        }

        return $events;
    }

    /**
     * Finds and adds learning materials to a given list of calendar events.
     *
     * @param CalendarEvent[] $events
     * @param UserMaterialFactory $factory
     * @param EntityManager $em
     * @return CalendarEvent[]
     */
    public function attachMaterialsToEvents(array $events, UserMaterialFactory $factory, EntityManager $em)
    {
        $sessionIds = array_map(function (CalendarEvent $event) {
            return $event->session;
        }, $events);

        $sessionIds = array_values(array_unique($sessionIds));

        $sessionMaterials = $this->getSessionLearningMaterials($sessionIds, $em);

        $sessionUserMaterials = array_map(function (array $arr) use ($factory) {
            return $factory->create($arr);
        }, $sessionMaterials);

        $courseMaterials = $this->getCourseLearningMaterials($sessionIds, $em);

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
            $sessionId = $event->session;
            $courseId = $event->courseId;
            $sessionLms = array_key_exists($sessionId, $groupedSessionLms) ? $groupedSessionLms[$sessionId] : [];
            $courseLms = array_key_exists($courseId, $groupedCourseLms) ? $groupedCourseLms[$courseId] : [];
            $lms = array_merge($sessionLms, $courseLms);
            $event->learningMaterials = $lms;
        }
        return $events;
    }

    /**
     * Get a set of learning materials based on published session
     *
     * @param array $sessionIds
     *
     * @param EntityManager $em
     * @return array
     */
    protected function getSessionLearningMaterialsForPublishedSessions(
        array $sessionIds,
        EntityManager $em
    ) {
        $qb = $this->sessionLmQuery($sessionIds, $em);
        $qb->andWhere($qb->expr()->eq('s.published', 1));
        $qb->andWhere($qb->expr()->eq('s.publishedAsTbd', 0));
        $qb->andWhere($qb->expr()->eq('c.published', 1));
        $qb->andWhere($qb->expr()->eq('c.publishedAsTbd', 0));

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * Get a set of learning materials based on session
     *
     * @param array $sessionIds
     *
     * @param EntityManager $em
     * @return array
     */
    protected function getSessionLearningMaterials(
        array $sessionIds,
        EntityManager $em
    ) {
        $qb = $this->sessionLmQuery($sessionIds, $em);
        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @param array $sessionIds
     * @param EntityManager $em
     * @return QueryBuilder
     */
    protected function sessionLmQuery(
        array $sessionIds,
        EntityManager $em
    ) {

        $qb = $em->createQueryBuilder();
        $what = 's.title as sessionTitle, s.id as sessionId, ' .
            'c.id as courseId, c.title as courseTitle, ' .
            'slm.id as slmId, slm.position, slm.notes, slm.required, slm.publicNotes, slm.startDate, slm.endDate, ' .
            'lm.id, lm.title, lm.description, lm.originalAuthor, lm.token, ' .
            'lm.citation, lm.link, lm.filename, lm.filesize, lm.mimetype, lms.id AS status';
        $qb->select($what)->from('App\Entity\Session', 's');
        $qb->join('s.learningMaterials', 'slm');
        $qb->join('slm.learningMaterial', 'lm');
        $qb->join('lm.status', 'lms');
        $qb->join('s.course', 'c');

        $qb->andWhere($qb->expr()->in('s.id', ':sessions'));
        $qb->setParameter(':sessions', $sessionIds);
        $qb->distinct();

        return $qb;
    }

    /**
     * Get a set of course learning materials
     *
     * @param array $sessionIds
     *
     * @param EntityManager $em
     * @return array
     */
    protected function getCourseLearningMaterials(
        array $sessionIds,
        EntityManager $em
    ) {
        $qb = $this->courseLmQuery($sessionIds, $em);

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * Get a set of course learning materials for published sessions
     *
     * @param array $sessionIds
     *
     * @param EntityManager $em
     * @return array
     */
    protected function getCourseLearningMaterialsForPublishedSessions(
        array $sessionIds,
        EntityManager $em
    ) {
        $qb = $this->courseLmQuery($sessionIds, $em);
        $qb->andWhere($qb->expr()->eq('c.published', 1));
        $qb->andWhere($qb->expr()->eq('c.publishedAsTbd', 0));

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @param array $sessionIds
     * @param EntityManager $em
     * @return QueryBuilder
     */
    protected function courseLmQuery(
        array $sessionIds,
        EntityManager $em
    ) {
        $qb = $em->createQueryBuilder();
        $what = 'c.title as courseTitle, c.id as courseId, c.startDate as firstOfferingDate, ' .
            'clm.id as clmId, clm.position, clm.notes, clm.required, clm.publicNotes, clm.startDate, clm.endDate, ' .
            'lm.id, lm.title, lm.description, lm.originalAuthor, lm.token, ' .
            'lm.citation, lm.link, lm.filename, lm.filesize, lm.mimetype, lms.id AS status';
        $qb->select($what)->from('App\Entity\Session', 's');
        $qb->join('s.course', 'c');
        $qb->join('c.learningMaterials', 'clm');
        $qb->join('clm.learningMaterial', 'lm');
        $qb->join('lm.status', 'lms');


        $qb->andWhere($qb->expr()->in('s.id', ':sessions'));
        $qb->setParameter(':sessions', $sessionIds);
        $qb->distinct();

        return $qb;
    }
}
