<?php

declare(strict_types=1);

namespace App\Traits;

use App\Entity\Competency;
use App\Entity\Course;
use App\Entity\Session;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
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
     */
    protected function createEventObjectsForOfferings(array $results, array $userContexts = []): array
    {
        return array_map(fn($arr) => $this->createEventObjectForOffering($arr, $userContexts), $results);
    }

    protected function createEventObjectForOffering(array $arr, array $userContexts = []): CalendarEvent
    {
        $event = new CalendarEvent();
        $event->name = $arr['title'];
        $event->startDate = $arr['startDate'];
        $event->endDate = $arr['endDate'];
        $event->offering = $arr['id'];
        $event->location = $arr['room'];
        $event->url = $arr['url'];
        $event->color = $arr['calendarColor'];
        $event->lastModified = max($arr['offeringUpdatedAt'], $arr['sessionUpdatedAt']);
        $event->isPublished = $arr['sessionPublished']  && $arr['coursePublished'];
        $event->isScheduled = $arr['sessionPublishedAsTbd'] || $arr['coursePublishedAsTbd'];
        $event->courseTitle = $arr['courseTitle'];
        $event->courseLevel = $arr['courseLevel'];
        $event->sessionTypeId = $arr['sessionTypeId'];
        $event->sessionTypeTitle = $arr['sessionTypeTitle'];
        $event->courseExternalId = $arr['courseExternalId'];
        $event->sessionDescription = $arr['sessionDescription'];
        $event->instructionalNotes = $arr['instructionalNotes'];
        $event->session = $arr['sessionId'];
        $event->course = $arr['courseId'];
        $event->attireRequired = $arr['attireRequired'];
        $event->equipmentRequired = $arr['equipmentRequired'];
        $event->supplemental = $arr['supplemental'];
        $event->attendanceRequired = $arr['attendanceRequired'];
        $event->school = $arr['schoolId'];
        $event->userContexts = $userContexts;
        return $event;
    }

    /**
     * Convert IlmSessions into CalendarEvent() objects
     */
    protected function createEventObjectsForIlmSessions(array $results, array $userContexts = []): array
    {
        return array_map(fn($arr) => $this->createEventObjectForIlmSession($arr, $userContexts), $results);
    }

    protected function createEventObjectForIlmSession(array $arr, array $userContexts = []): CalendarEvent
    {
        $event = new CalendarEvent();
        $event->name = $arr['title'];
        $event->startDate = $arr['dueDate'];
        $endDate = new DateTime();
        $endDate->setTimestamp($event->startDate->getTimestamp());
        $event->endDate = $endDate->modify('+15 minutes');
        $event->ilmSession = $arr['id'];
        $event->color = $arr['calendarColor'];
        $event->lastModified = $arr['updatedAt'];
        $event->isPublished = $arr['sessionPublished']  && $arr['coursePublished'];
        $event->isScheduled = $arr['sessionPublishedAsTbd'] || $arr['coursePublishedAsTbd'];
        $event->courseTitle = $arr['courseTitle'];
        $event->courseLevel = $arr['courseLevel'];
        $event->sessionTypeTitle = $arr['sessionTypeTitle'];
        $event->sessionTypeId = $arr['sessionTypeId'];
        $event->courseExternalId = $arr['courseExternalId'];
        $event->sessionDescription = $arr['sessionDescription'];
        $event->session = $arr['sessionId'];
        $event->course = $arr['courseId'];
        $event->attireRequired = $arr['attireRequired'];
        $event->equipmentRequired = $arr['equipmentRequired'];
        $event->supplemental = $arr['supplemental'];
        $event->attendanceRequired = $arr['attendanceRequired'];
        $event->school = $arr['schoolId'];
        $event->userContexts = $userContexts;
        return $event;
    }

    /**
     * Retrieves a list of instructors associated with given offerings.
     */
    protected function getInstructorsForOfferings(array $ids, EntityManagerInterface $em): array
    {
        if (empty($ids)) {
            return [];
        }

        $qb = $em->createQueryBuilder();
        $qb->select('o.id AS oId, u.id AS userId, u.firstName, u.lastName, u.displayName, u.pronouns')
            ->from(User::class, 'u');
        $qb->leftJoin('u.instructedOfferings', 'o');
        $qb->where(
            $qb->expr()->in('o.id', ':offerings')
        );
        $qb->setParameter(':offerings', $ids);
        $instructedOfferings = $qb->getQuery()->getArrayResult();


        $qb = $em->createQueryBuilder();
        $qb->select('o.id AS oId, u.id AS userId, u.firstName, u.lastName, u.displayName, u.pronouns')
            ->from(User::class, 'u');
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
            if (!empty($result['displayName'])) {
                $name = $result['displayName'];
            } else {
                $name = $result['firstName'] . ' ' . $result['lastName'];
            }
            if (!empty($result['pronouns'])) {
                $name .= " ({$result['pronouns']})";
            }
            $offeringInstructors[$result['oId']][$result['userId']] = $name;
        }
        return $offeringInstructors;
    }

    /**
     * Retrieves a list of instructors associated with given ILM sessions.
     *
     * @param array $ids A list of ILM session ids.
     */
    protected function getInstructorsForIlmSessions(array $ids, EntityManagerInterface $em): array
    {
        if (empty($ids)) {
            return [];
        }

        $qb = $em->createQueryBuilder();
        $qb->select('ilm.id AS ilmId, u.id AS userId, u.firstName, u.lastName, u.displayName')
            ->from(User::class, 'u');
        $qb->leftJoin('u.instructorIlmSessions', 'ilm');
        $qb->where(
            $qb->expr()->in('ilm.id', ':ilms')
        );
        $qb->setParameter(':ilms', $ids);
        $instructedIlms = $qb->getQuery()->getArrayResult();

        $qb = $em->createQueryBuilder();
        $qb->select('ilm.id AS ilmId, u.id AS userId, u.firstName, u.lastName, u.displayName')
            ->from(User::class, 'u');
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
            $name = $result['displayName'] ?? $result['firstName'] . ' ' . $result['lastName'];
            $ilmInstructors[$result['ilmId']][$result['userId']] = $name;
        }
        return $ilmInstructors;
    }

    /**
     * Adds instructors to a given list of events.
     */
    public function attachInstructorsToEvents(array $events, EntityManagerInterface $em): array
    {
        $offeringIds = array_map(fn($event) => $event->offering, array_filter($events, fn($event) => $event->offering));

        $ilmIds = array_map(fn($event) => $event->ilmSession, array_filter($events, fn($event) => $event->ilmSession));

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
     * Adds course and session data to a given list of events.
     * @param array $events A list of events
     */
    public function attachSessionDataToEvents(array $events, EntityManagerInterface $em): array
    {
        $sessionIds = array_unique(array_column($events, 'session'));
        $courseIds = array_unique(array_column($events, 'course'));
        $competencyIds = [];

        $qb = $em->createQueryBuilder();
        $qb->select('s.id AS session_id, so.id, so.title, so.position, cm.id AS competency_id')
            ->distinct()
            ->from(Session::class, 's')
            ->join('s.sessionObjectives', 'so')
            ->leftJoin('so.courseObjectives', 'co')
            ->leftJoin('co.programYearObjectives', 'po')
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

            if (
                ! empty($competencyId)
                && ! in_array($competencyId, $sessionObjectives[$sessionId][$objectiveId]['competencies'])
            ) {
                $sessionObjectives[$sessionId][$objectiveId]['competencies'][] = $competencyId;
            }

            $competencyIds[] = $competencyId;
        }

        $qb = $em->createQueryBuilder();
        $qb->select('c.id AS course_id, co.id, co.title, co.position, cm.id AS competency_id')
            ->distinct()
            ->from(Course::class, 'c')
            ->join('c.courseObjectives', 'co')
            ->leftJoin('co.programYearObjectives', 'po')
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

            if (
                ! empty($competencyId)
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
            ->from(Competency::class, 'cm')
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

        $courseCohorts = $this->getCohortsForCourses($courseIds, $em);
        $courseTerms = $this->getTermsForCourses($courseIds, $em);
        $sessionTerms = $this->getTermsForSessions($sessionIds, $em);

        return array_map(function (CalendarEvent $event) use (
            $sessionObjectives,
            $courseObjectives,
            $competencies,
            $courseCohorts,
            $courseTerms,
            $sessionTerms
        ) {
            if (array_key_exists($event->session, $sessionObjectives)) {
                $event->sessionObjectives = array_values($sessionObjectives[$event->session]);
            }
            if (array_key_exists($event->course, $courseObjectives)) {
                $event->courseObjectives = array_values($courseObjectives[$event->course]);
            }

            $listsOfCompetencyIds = array_merge(
                array_column($event->sessionObjectives, 'competencies'),
                array_column($event->courseObjectives, 'competencies')
            );
            $competencyIds = [];
            // flatted out lists of competency ids
            // @link https://stackoverflow.com/a/1320156
            array_walk_recursive($listsOfCompetencyIds, function ($a) use (&$competencyIds): void {
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

            if (array_key_exists($event->course, $courseCohorts)) {
                $event->cohorts = array_values($courseCohorts[$event->course]);
            }

            if (array_key_exists($event->course, $courseTerms)) {
                $event->courseTerms = array_values($courseTerms[$event->course]);
            }

            if (array_key_exists($event->session, $sessionTerms)) {
                $event->sessionTerms = array_values($sessionTerms[$event->session]);
            }
            return $event;
        }, $events);
    }

    /**
     * Finds and adds learning materials to a given list of calendar events.
     */
    public function attachMaterialsToEvents(
        array $events,
        UserMaterialFactory $factory,
        EntityManagerInterface $em
    ): array {
        $sessionIds = array_map(fn(CalendarEvent $event) => $event->session, $events);

        $sessionIds = array_values(array_unique($sessionIds));

        $sessionMaterials = $this->getSessionLearningMaterials($sessionIds, $em);

        $sessionUserMaterials = array_map(fn(array $arr) => $factory->create($arr), $sessionMaterials);

        $courseMaterials = $this->getCourseLearningMaterials($sessionIds, $em);

        $courseUserMaterials = array_map(fn(array $arr) => $factory->create($arr), $courseMaterials);



        //sort materials by id for consistency
        $sortFn = fn(UserMaterial $a, UserMaterial $b) => $a->id - $b->id;

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

        for ($i = 0, $n = count($events); $i < $n; $i++) {
            $event = $events[$i];
            $sessionId = $event->session;
            $courseId = $event->course;
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
     */
    protected function getSessionLearningMaterialsForPublishedSessions(
        array $sessionIds,
        EntityManagerInterface $em
    ): array {
        $qb = $this->sessionLmQuery($sessionIds, $em);
        $qb->andWhere($qb->expr()->eq('s.published', 1));
        $qb->andWhere($qb->expr()->eq('s.publishedAsTbd', 0));
        $qb->andWhere($qb->expr()->eq('c.published', 1));
        $qb->andWhere($qb->expr()->eq('c.publishedAsTbd', 0));

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * Get a set of learning materials based on session
     */
    protected function getSessionLearningMaterials(
        array $sessionIds,
        EntityManagerInterface $em
    ): array {
        $qb = $this->sessionLmQuery($sessionIds, $em);
        return $qb->getQuery()->getArrayResult();
    }

    protected function sessionLmQuery(
        array $sessionIds,
        EntityManagerInterface $em
    ): QueryBuilder {

        $qb = $em->createQueryBuilder();
        $what = 's.title as sessionTitle, s.id as sessionId, ' .
            'c.id as courseId, c.title as courseTitle, c.year as courseYear, c.externalId as courseExternalId, ' .
            'slm.id as slmId, slm.position, slm.notes, slm.required, slm.publicNotes, slm.startDate, slm.endDate, ' .
            'lm.id, lm.title, lm.description, lm.originalAuthor, lm.token, ' .
            'lm.citation, lm.link, lm.filename, lm.filesize, lm.mimetype, lms.id AS status';
        $qb->select($what)->from(Session::class, 's');
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
     */
    protected function getCourseLearningMaterials(
        array $sessionIds,
        EntityManagerInterface $em
    ): array {
        $qb = $this->courseLmQuery($sessionIds, $em);

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * Get a set of course learning materials for published sessions
     */
    protected function getCourseLearningMaterialsForPublishedSessions(
        array $sessionIds,
        EntityManagerInterface $em
    ): array {
        $qb = $this->courseLmQuery($sessionIds, $em);
        $qb->andWhere($qb->expr()->eq('c.published', 1));
        $qb->andWhere($qb->expr()->eq('c.publishedAsTbd', 0));

        return $qb->getQuery()->getArrayResult();
    }

    protected function courseLmQuery(
        array $sessionIds,
        EntityManagerInterface $em
    ): QueryBuilder {
        $qb = $em->createQueryBuilder();
        $what = 'c.title as courseTitle, c.year as courseYear, c.externalId as courseExternalId, ' .
            'c.id as courseId, c.startDate as firstOfferingDate, ' .
            'clm.id as clmId, clm.position, clm.notes, clm.required, clm.publicNotes, clm.startDate, clm.endDate, ' .
            'lm.id, lm.title, lm.description, lm.originalAuthor, lm.token, ' .
            'lm.citation, lm.link, lm.filename, lm.filesize, lm.mimetype, lms.id AS status';
        $qb->select($what)->from(Session::class, 's');
        $qb->join('s.course', 'c');
        $qb->join('c.learningMaterials', 'clm');
        $qb->join('clm.learningMaterial', 'lm');
        $qb->join('lm.status', 'lms');


        $qb->andWhere($qb->expr()->in('s.id', ':sessions'));
        $qb->setParameter(':sessions', $sessionIds);
        $qb->distinct();

        return $qb;
    }

    protected function getCohortsForCourses(array $courseIds, EntityManagerInterface $em): array
    {
        $qb = $em->createQueryBuilder();
        $qb->select('c.id AS courseId, co.id, co.title')
            ->distinct()
            ->from(Course::class, 'c')
            ->join('c.cohorts', 'co')
            ->where($qb->expr()->in('c.id', ':ids'))
            ->setParameter(':ids', $courseIds);

        $results = $qb->getQuery()->getArrayResult();

        $courseCohorts =  [];

        foreach ($results as $result) {
            $courseId = $result['courseId'];
            $cohortId = $result['id'];

            if (! array_key_exists($courseId, $courseCohorts)) {
                $courseCohorts[$courseId] = [];
            }
            if (! array_key_exists($cohortId, $courseCohorts[$courseId])) {
                $courseCohorts[$courseId][$cohortId] = [
                    'id' => $cohortId,
                    'title' => $result['title'],
                ];
            }
        }

        return $courseCohorts;
    }

    protected function getTermsForCourses(array $courseIds, EntityManagerInterface $em): array
    {
        $qb = $em->createQueryBuilder();
        $qb->select('c.id, t.id as termId, t.title as termTitle, v.id as vocabularyId, v.title as vocabularyTitle')
            ->distinct()
            ->from(Course::class, 'c')
            ->join('c.terms', 't')
            ->leftJoin('t.vocabulary', 'v')
            ->where($qb->expr()->in('c.id', ':ids'))
            ->setParameter(':ids', $courseIds);

        $results = $qb->getQuery()->getArrayResult();

        return $this->parseTermResults($results);
    }

    protected function getTermsForSessions(array $sessionIds, EntityManagerInterface $em): array
    {
        $qb = $em->createQueryBuilder();
        $qb->select('s.id, t.id as termId, t.title as termTitle, v.id as vocabularyId, v.title as vocabularyTitle')
            ->distinct()
            ->from(Session::class, 's')
            ->join('s.terms', 't')
            ->leftJoin('t.vocabulary', 'v')
            ->where($qb->expr()->in('s.id', ':ids'))
            ->setParameter(':ids', $sessionIds);

        $results = $qb->getQuery()->getArrayResult();

        return $this->parseTermResults($results);
    }

    protected function parseTermResults(array $results): array
    {
        $terms = [];
        foreach ($results as $result) {
            $id = $result['id'];
            $termId = $result['termId'];

            if (! array_key_exists($id, $terms)) {
                $terms[$id] = [];
            }
            if (! array_key_exists($termId, $terms[$id])) {
                $terms[$id][$termId] = [
                    'id' => $termId,
                    'title' => $result['termTitle'],
                    'vocabularyId' => $result['vocabularyId'],
                    'vocabularyTitle' => $result['vocabularyTitle'],
                ];
            }
        }

        return $terms;
    }
}
