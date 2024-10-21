<?php

declare(strict_types=1);

namespace App\Service\CurriculumInventory;

use App\Entity\CurriculumInventoryReportInterface;
use App\Repository\CurriculumInventoryReportRepository;
use DateTime;
use Exception;

/**
 * Read-only wrapper-service around the Curriculum Inventory (CI) report repository.
 * Applies data transformations to the data retrieved from the repo in most cases.
 * Use this as gateway to CI data when generating reports, rather than dealing with
 * the repo directly.
 */
class Manager
{
    public function __construct(protected CurriculumInventoryReportRepository $repository)
    {
    }

    /**
     * Retrieves a list of events (derived from published sessions/offerings and independent learning sessions)
     * in a given curriculum inventory report.
     *
     * @return array An assoc. array of assoc. arrays, each item representing an event, keyed off by event id.
     * @throws Exception
     */
    public function getEvents(CurriculumInventoryReportInterface $report): array
    {
        // WHAT'S GOING ON HERE?!
        // Aggregate the CI events retrieved from session-offerings with the events retrieved from ILM sessions,
        // and sessions that are ILMs with offerings.
        // We can't do this by ways of <code>array_merge()</code>, since this would clobber the keys on the joined array
        // (we're dealing with associative arrays using numeric keys here).
        // Hence the use of the '+' array-operator.
        // This should be OK since there is no overlap of elements between the various source arrays.
        // [ST 2015/09/18]
        // @link http://php.net/manual/en/language.operators.array.php
        // @link http://php.net/manual/en/function.array-merge.php
        $sessionIds = $this->repository->getCountForOneOfferingSessionIds($report);
        $excludedSessionids = $this->repository->getExcludedSessionIds($report);
        $rhett = $this->getEventsFromOfferingsOnlySessions($report, $sessionIds, $excludedSessionids)
            + $this->getEventsFromIlmOnlySessions($report, $excludedSessionids)
            + $this->getEventsFromIlmSessionsWithOfferings($report, $sessionIds, $excludedSessionids);
        ksort($rhett);
        return $rhett;
    }

    /**
     * Retrieves AAMC resource types associated with given events (sessions) in a given curriculum inventory report.
     *
     * @param array|int[] $eventIds
     * @return array An array of assoc. arrays, each sub-array representing a resource type.
     * @throws Exception
     */
    public function getEventResourceTypes(CurriculumInventoryReportInterface $report, array $eventIds = []): array
    {
        return $this->repository->getEventResourceTypes($report, $eventIds);
    }

    /**
     * Retrieves keywords (MeSH descriptors) associated with events (sessions)
     * in a given curriculum inventory report.
     *
     * @param array|int[] $eventIds
     * @return array An array of assoc. arrays, each sub-array representing a keyword.
     * @throws Exception
     */
    public function getEventKeywords(CurriculumInventoryReportInterface $report, array $eventIds = []): array
    {
        return $this->repository->getEventKeywords($report, $eventIds);
    }

    /**
     * Retrieves a lookup map of given events ('sessions') in a given curriculum inventory report,
     * grouped and keyed off by sequence block id.
     * @param array|int[] $eventIds
     * @throws Exception
     */
    public function getEventReferencesForSequenceBlocks(
        CurriculumInventoryReportInterface $report,
        array $eventIds = []
    ): array {
        $rhett = [];
        $rows = $this->repository->getEventReferencesForSequenceBlocks($report, $eventIds);

        foreach ($rows as $row) {
            if (! array_key_exists($row['id'], $rhett)) {
                $rhett[$row['id']] = [];
            }
            $rhett[$row['id']][] = $row;
        }

        return $rhett;
    }

    /**
     * Retrieves all program objectives in a given curriculum inventory report.
     *
     * @return array An associative array of arrays, keyed off by objective id.
     *   Each item is an associative array, containing
     *   the objective's id, title and its ancestor's id.
     *  (keys: "objective_id", "title" and "ancestor_id").
     * @throws Exception
     */
    public function getProgramObjectives(CurriculumInventoryReportInterface $report): array
    {
        return $this->repository->getProgramObjectives($report);
    }

    /**
     * Retrieves all session objectives for given sessions in a given curriculum inventory report.
     *
     * @param array|int[] $sessionIds
     * @return array An associative array of arrays, keyed off by objective id.
     *   Each item is an associative array, containing
     *   the objective's id and title (keys: "objective_id" and "title").
     * @throws Exception
     */
    public function getSessionObjectives(CurriculumInventoryReportInterface $report, array $sessionIds = []): array
    {
        return $this->repository->getSessionObjectives($report, $sessionIds);
    }

    /**
     * Retrieves all course objectives in a given curriculum inventory report.
     *
     * @return array an associative array of arrays, keyed off by objective id.
     *   Each item is an associative array, containing
     *   the objective's id and title (keys: "objective_id" and "title").
     * @throws Exception
     */
    public function getCourseObjectives(CurriculumInventoryReportInterface $report): array
    {
        return $this->repository->getCourseObjectives($report);
    }

    /**
     * Retrieves all PCRS linked to sequence blocks (via objectives and competencies) in a given inventory report.
     *
     * @return array A nested array of associative arrays, keyed off by 'pcrs_id'. Each sub-array represents a PCRS
     *    and is itself an associative array with values being keyed off by 'pcrs_id' and 'description'.
     * @throws Exception
     */
    public function getPcrs(CurriculumInventoryReportInterface $report): array
    {
        return $this->repository->getPcrs($report);
    }

    /**
     * Retrieves all the competency object references per sequence block in a given report.
     *
     * @param array|int[] $consolidatedProgramObjectivesMap
     * @return array An associative array of arrays, keyed off by sequence block id.
     *     Each sub-array is in turn a two item map, containing a list of course objectives ids
     *     under 'course_objectives' and a list of program objective ids under 'program_objective'.
     *
     *   <pre>
     *   [ <sequence block id> => [
     *       "course_objectives" => [ <list of course objectives ids> ]
     *       "program_objectives" => [ <list of program objective ids> ]
     *     ],
     *     ...
     *   ],
     *   </pre>
     * @throws Exception
     */
    public function getCompetencyObjectReferencesForSequenceBlocks(
        CurriculumInventoryReportInterface $report,
        array $consolidatedProgramObjectivesMap
    ): array {
        $rhett = [];
        $rows = $this->repository->getCompetencyObjectReferencesForSequenceBlocks($report);

        foreach ($rows as $row) {
            $sequenceBlockId = $row['id'];
            $courseObjectiveId = $row['course_objective_id'];
            $programObjectiveId = $row['program_objective_id'];
            if (array_key_exists($programObjectiveId, $consolidatedProgramObjectivesMap)) {
                $programObjectiveId = $consolidatedProgramObjectivesMap[$programObjectiveId];
            }
            if (! array_key_exists($sequenceBlockId, $rhett)) {
                $rhett[$sequenceBlockId] = [
                    'course_objectives' => [],
                    'program_objectives' => [],
                ];
            }
            if (
                isset($courseObjectiveId)
                && ! in_array($courseObjectiveId, $rhett[$sequenceBlockId]['course_objectives'])
            ) {
                $rhett[$sequenceBlockId]['course_objectives'][] = $courseObjectiveId;
            }
            if (
                isset($programObjectiveId)
                && ! in_array($programObjectiveId, $rhett[$sequenceBlockId]['program_objectives'])
            ) {
                $rhett[$sequenceBlockId]['program_objectives'][] = $programObjectiveId;
            }
        }

        return $rhett;
    }

    /**
     * Retrieves all the competency object references per given event (session) in a given report.
     *
     * @param array|int[] $consolidatedProgramObjectivesMap
     * @param array|int[] $eventIds
     * @return array An associative array of arrays, keyed off by event id.
     *     Each sub-array is in turn a two item map, containing a list of course objectives ids
     *     under 'course_objectives', a list of program objective ids under 'program_objective'
     *     and a list of session objective ids under under 'session_objective_ids'.
     *
     *   <pre>
     *   [ <sequence block id> => [
     *       "course_objectives" => [ <list of course objectives ids> ]
     *       "program_objectives" => [ <list of program objective ids> ]
     *       "session_objectives" => [ <list of session objective ids> ]
     *     ],
     *     ...
     *   ],
     *   </pre>
     * @throws Exception
     */
    public function getCompetencyObjectReferencesForEvents(
        CurriculumInventoryReportInterface $report,
        array $consolidatedProgramObjectivesMap,
        array $eventIds = []
    ): array {
        $rhett = [];

        $rows = $this->repository->getCompetencyObjectReferencesForEvents(
            $report,
            $eventIds
        );

        foreach ($rows as $row) {
            $eventId = $row['event_id'];
            $sessionObjectiveId = $row['session_objective_id'];
            $courseObjectiveId = $row['course_objective_id'];
            $programObjectiveId = $row['program_objective_id'];
            if (array_key_exists($programObjectiveId, $consolidatedProgramObjectivesMap)) {
                $programObjectiveId = $consolidatedProgramObjectivesMap[$programObjectiveId];
            }
            if (! array_key_exists($eventId, $rhett)) {
                $rhett[$eventId] = [
                    'session_objectives' => [],
                    'course_objectives' => [],
                    'program_objectives' => [],
                ];
            }
            if (
                isset($sessionObjectiveId)
                && ! in_array($sessionObjectiveId, $rhett[$eventId]['session_objectives'])
            ) {
                $rhett[$eventId]['session_objectives'][] = $sessionObjectiveId;
            }
            if (
                isset($courseObjectiveId)
                && ! in_array($courseObjectiveId, $rhett[$eventId]['course_objectives'])
            ) {
                $rhett[$eventId]['course_objectives'][] = $courseObjectiveId;
            }
            if (
                isset($programObjectiveId)
                && ! in_array($programObjectiveId, $rhett[$eventId]['program_objectives'])
            ) {
                $rhett[$eventId]['program_objectives'][] = $programObjectiveId;
            }
        }

        return $rhett;
    }

    /**
     * Retrieves the relations between given session- and course-objectives.
     *
     * @param array|int[] $sessionObjectiveIds
     * @param array|int[] $courseObjectiveIds
     * @throws Exception
     */
    public function getSessionObjectivesToCourseObjectivesRelations(
        array $sessionObjectiveIds,
        array $courseObjectiveIds
    ): array {
        $rhett = [
            'relations' => [],
            'session_objective_ids' => [],
            'course_objective_ids' => [],
        ];

        $rows = $this->repository->getSessionObjectivesToCourseObjectivesRelations(
            $sessionObjectiveIds,
            $courseObjectiveIds
        );

        foreach ($rows as $row) {
            $rhett['relations'][] = [
                'rel1' => $row['course_objective_id'],
                'rel2' => $row['objective_id'],
            ];
            $rhett['session_objective_ids'][] = $row['objective_id'];
            $rhett['course_objective_ids'][] = $row['course_objective_id'];
        }

        // dedupe
        $rhett['session_objective_ids'] = array_values(array_unique($rhett['session_objective_ids']));
        $rhett['course_objective_ids'] = array_values(array_unique($rhett['course_objective_ids']));

        return $rhett;
    }

    /**
     * Retrieves the relations between given course- and program-objectives.
     * @param array|int[] $courseObjectiveIds
     * @param array|int[] $programObjectiveIds
     * @param array|int[] $consolidatedProgramObjectivesMap
     * @throws Exception
     */
    public function getCourseObjectivesToProgramObjectivesRelations(
        array $courseObjectiveIds,
        array $programObjectiveIds,
        array $consolidatedProgramObjectivesMap
    ): array {
        $rhett = [
            'relations' => [],
            'course_objective_ids' => [],
            'program_objective_ids' => [],
        ];

        $rows = $this->repository->getCourseObjectivesToProgramObjectivesRelations(
            $courseObjectiveIds,
            $programObjectiveIds
        );

        foreach ($rows as $row) {
            $programObjectiveId = $row['program_objective_id'];
            $courseObjectiveId = $row['objective_id'];
            if (array_key_exists($programObjectiveId, $consolidatedProgramObjectivesMap)) {
                $programObjectiveId = $consolidatedProgramObjectivesMap[$programObjectiveId];
            }
            $relKey = $programObjectiveId . ':' . $courseObjectiveId; // poor man's way to avoid duplication
            $rhett['relations'][$relKey] = [
                'rel1' => $programObjectiveId,
                'rel2' => $courseObjectiveId,
            ];

            $rhett['course_objective_ids'][] = $courseObjectiveId;
            $rhett['program_objective_ids'][] = $programObjectiveId;
        }

        // dedupe
        $rhett['course_objective_ids'] = array_values(array_unique($rhett['course_objective_ids']));
        $rhett['program_objective_ids'] = array_values(array_unique($rhett['program_objective_ids']));

        // lose the temp key
        $rhett['relations'] = array_values($rhett['relations']);

        return $rhett;
    }

    /**
     * Retrieves the relations between given program-objectives and PCRS (via competencies).
     * @param array|int[] $programObjectiveIds
     * @param array|int[] $pcrsIds
     * @param array|int[] $consolidatedProgramObjectivesMap
     * @throws Exception
     */
    public function getProgramObjectivesToPcrsRelations(
        array $programObjectiveIds,
        array $pcrsIds,
        array $consolidatedProgramObjectivesMap
    ): array {
        $rhett = [
            'relations' => [],
            'program_objective_ids' => [],
            'pcrs_ids' => [],
        ];

        $rows = $this->repository->getProgramObjectivesToPcrsRelations(
            $programObjectiveIds,
            $pcrsIds
        );

        foreach ($rows as $row) {
            $pcrsId = $row['pcrs_id'];
            $objectiveId = $row['objective_id'];
            // ignore substituted objectives here, in order to prevent
            // false objective-to-PCRS relationships from being reported out.
            if (array_key_exists($objectiveId, $consolidatedProgramObjectivesMap)) {
                continue;
            }
            $rhett['relations'][] = [
                'rel1' => $objectiveId,
                'rel2' => $pcrsId,
            ];
            $rhett['program_objective_ids'][] = $objectiveId;
            $rhett['pcrs_ids'][] = $pcrsId;
        }

        // dedupe
        $rhett['program_objective_ids'] = array_values(array_unique($rhett['program_objective_ids']));
        $rhett['pcrs_ids'] = array_values(array_unique($rhett['pcrs_ids']));

        return $rhett;
    }

    /**
     * Retrieves a list of events (derived from published sessions/offerings)
     * in a given curriculum inventory report.
     *
     * @param array $sessionIds The ids of sessions that are flagged to have their offerings counted as one.
     * @param array $excludedSessionIds The ids of sessions that are flagged to be excluded from this report.
     * @return array An assoc. array of assoc. arrays, each item representing an event, keyed off by event id.
     * @throws Exception
     */
    public function getEventsFromOfferingsOnlySessions(
        CurriculumInventoryReportInterface $report,
        array $sessionIds = [],
        array $excludedSessionIds = []
    ): array {
        $rhett = [];

        $rows = $this->repository->getEventsFromOfferingsOnlySessions($report, $excludedSessionIds);

        foreach ($rows as $row) {
            $row['duration'] = 0;
            if ($row['startDate']) {
                /** @var DateTime $startDate */
                $startDate = $row['startDate'];
                /** @var DateTime $endDate */
                $endDate = $row['endDate'];
                $duration = floor(($endDate->getTimestamp() - $startDate->getTimestamp()) / 60);
                $row['duration'] = $duration;
            }

            if (!array_key_exists($row['event_id'], $rhett)) {
                $rhett[$row['event_id']] = $row;
            } elseif (in_array($row['event_id'], $sessionIds)) {
                if ($rhett[$row['event_id']]['duration'] < $row['duration']) {
                    $rhett[$row['event_id']]['duration'] = $row['duration'];
                }
            } else {
                $rhett[$row['event_id']]['duration'] += $row['duration'];
            }
        }

        array_walk($rhett, function (&$row): void {
            unset($row['startDate']);
            unset($row['endDate']);
        });
        return $rhett;
    }

    /**
     * Retrieves a list of events derived from independent learning sessions in a given curriculum inventory report.
     *
     * @param array $excludedSessionIds The ids of sessions that are flagged to be excluded from this report.
     * @return array An assoc. array of assoc. arrays, each item representing an event, keyed off by event id.
     * @throws Exception
     */
    public function getEventsFromIlmOnlySessions(
        CurriculumInventoryReportInterface $report,
        array $excludedSessionIds = []
    ): array {
        $rhett = [];

        $rows = $this->repository->getEventsFromIlmOnlySessions($report, $excludedSessionIds);

        foreach ($rows as $row) {
            $row['duration'] = floor($row['hours'] * 60); // convert from hours to minutes
            unset($row['hours']);
            $rhett[$row['event_id']] = $row;
        }
        return $rhett;
    }

    /**
     * Retrieves a list of events (derived from published ILM sessions with offerings)
     * in a given curriculum inventory report.
     *
     * @param array $sessionIds The ids of sessions that are flagged to have their offerings counted as one.
     * @param array $excludedSessionIds The ids of sessions that are flagged to be excluded from this report.
     * @return array An assoc. array of assoc. arrays, each item representing an event, keyed off by event id.
     * @throws Exception
     */
    public function getEventsFromIlmSessionsWithOfferings(
        CurriculumInventoryReportInterface $report,
        array $sessionIds = [],
        array $excludedSessionIds = []
    ): array {
        $rhett = [];

        $rows = $this->repository->getEventsFromIlmSessionsWithOfferings($report, $excludedSessionIds);

        $ilmHours = [];
        foreach ($rows as $row) {
            $ilmHours[$row['event_id']] =  floor($row['ilm_hours'] * 60);
            $row['duration'] = 0;
            if ($row['startDate']) {
                /** @var DateTime $startDate */
                $startDate = $row['startDate'];
                /** @var DateTime $endDate */
                $endDate = $row['endDate'];
                $duration = floor(($endDate->getTimestamp() - $startDate->getTimestamp()) / 60);
                $row['duration'] = $duration;
            }

            if (!array_key_exists($row['event_id'], $rhett)) {
                $rhett[$row['event_id']] = $row;
            } elseif (in_array($row['event_id'], $sessionIds)) {
                if ($rhett[$row['event_id']]['duration'] < $row['duration']) {
                    $rhett[$row['event_id']]['duration'] = $row['duration'];
                }
            } else {
                $rhett[$row['event_id']]['duration'] += $row['duration'];
            }
        }

        array_walk($rhett, function (&$row) use ($ilmHours): void {
            $row['duration'] = $row['duration'] + $ilmHours[$row['event_id']];
            unset($row['startDate']);
            unset($row['endDate']);
            unset($row['ilm_hours']);
        });

        return $rhett;
    }
}
