<?php

declare(strict_types=1);

namespace App\Service\CurriculumInventory\Export;

use App\Entity\CurriculumInventoryInstitutionInterface;
use App\Entity\CurriculumInventoryReportInterface;
use App\Repository\CurriculumInventoryInstitutionRepository;
use App\Service\Config;
use App\Service\CurriculumInventory\Manager;
use Exception;

/**
 * Data aggregator for Curriculum Inventory reporting.
 */
class Aggregator
{
    public function __construct(
        protected Manager $manager,
        protected CurriculumInventoryInstitutionRepository $institutionRepository,
        protected Config $config
    ) {
    }

    /**
     * Adds keywords to events.
     * @param array $events A list of events.
     * @param array $keywords A list of keywords.
     */
    public static function addKeywordsToEvents(array $events, array $keywords): array
    {
        foreach ($keywords as $keyword) {
            $eventId = $keyword['event_id'];
            if (! array_key_exists($eventId, $events)) {
                continue;
            }
            if (! array_key_exists('keywords', $events[$eventId])) {
                $events[$eventId]['keywords'] = [];
            }
            $events[$eventId]['keywords'][] = $keyword;
        }
        return $events;
    }

    /**
     * Adds AAMC resource types to events.
     * @param array $events A list of events.
     * @param array $resourceTypes A list of resource types.
     */
    public static function addResourceTypesToEvents(array $events, array $resourceTypes): array
    {
        foreach ($resourceTypes as $resourceType) {
            $eventId = $resourceType['event_id'];
            if (! array_key_exists($eventId, $events)) {
                continue;
            }
            if (! array_key_exists('resource_types', $events[$eventId])) {
                $events[$eventId]['resource_types'] = [];
            }
            $events[$eventId]['resource_types'][] = $resourceType;
        }
        return $events;
    }

    /**
     * Adds competency objects references to events.
     * @param array $events A list of events.
     * @param array $references A list of competency object references.
     */
    public static function addCompetencyObjectReferencesToEvents(array $events, array $references): array
    {
        $sessionIds = array_keys($events);
        for ($i = 0, $n = count($sessionIds); $i < $n; $i++) {
            $sessionId = $sessionIds[$i];
            if (array_key_exists($sessionId, $references)) {
                $events[$sessionId]['competency_object_references'] = $references[$sessionId];
            }
        }
        return $events;
    }

    /**
     * Retrieves a curriculum inventory in a data structure that lends itself for an easy transformation into
     * XML-formatted report.
     *
     * @param CurriculumInventoryReportInterface $invReport The report object.
     * @return array An associated array, containing the inventory.
     *     Data is keyed off by:
     *         'report' ... The inventory report entity.
     *         'created_at' ... UNIX timestamp indicating when this report was created.
     *         'supporting_link' ... A link to supporting information of the curriculum.
     *         'institution_domain' ... URN part of the report id.
     *         'institution' ... An object representing the curriculum inventory's owning institution
     *         'events' ... An array of events, keyed off by event id. Each event is represented as assoc. array.
     *         'expectations' ... An associative array of arrays, each sub-array containing a
     *                            list of a different type of "competency object" within the curriculum.
     *                            These types are program objectives, course objectives and session objectives.
     *                            The keys for these type-specific sub-arrays are:
     *             'program_objectives'
     *             'course_objectives'
     *             'session_objectives'
     *             'framework' ... The competency framework data set.
     *                 'includes' ... Identifiers of the various competency objects referenced in the framework.
     *                     'pcrs_ids'
     *                     'program_objective_ids'
     *                     'course_objective_ids'
     *                     'session_objective_ids'
     *                 'relations' ... Relations between the various competencies within the framework
     *                     'program_objectives_to_pcrs'
     *                     'course_objectives_to_program_objectives'
     *                     'session_objectives_to_course_objectives'
     *         'sequence_block_references' ...relationships maps between sequence blocks and other curricular entities.
     *             'events' ... maps sequence blocks to events
     *             'competency_objects' .. maps sequence blocks to competency objects
     *
     * @throws Exception
     */
    public function getData(CurriculumInventoryReportInterface $invReport): array
    {
        // report validation
        $program = $invReport->getProgram();
        if (! $program) {
            throw new Exception('No program found for report with id = ' . $invReport->getId() . '.');
        }

        $school  = $program->getSchool();

        $institution = $this->institutionRepository->findOneBy(['school' => $school->getId()]);
        if (! $institution) {
            throw new Exception(
                'No curriculum inventory institution found for school with id = ' . $school->getId() . '.'
            );
        }

        $events = $this->manager->getEvents($invReport);
        $eventIds = array_keys($events);
        $keywords = $this->manager->getEventKeywords($invReport, $eventIds);
        $resourceTypes = $this->manager->getEventResourceTypes($invReport, $eventIds);

        $eventRefsForSeqBlocks = $this->manager->getEventReferencesForSequenceBlocks($invReport, $eventIds);

        $programObjectives = $this->manager->getProgramObjectives($invReport);
        $consolidatedProgramObjectivesMap = self::getConsolidatedObjectivesMap($programObjectives);
        $sessionObjectives = $this->manager->getSessionObjectives($invReport, $eventIds);
        $courseObjectives = $this->manager->getCourseObjectives($invReport);

        $compObjRefsForSeqBlocks = $this->manager->getCompetencyObjectReferencesForSequenceBlocks(
            $invReport,
            $consolidatedProgramObjectivesMap
        );
        $compRefsForEvents = $this->manager->getCompetencyObjectReferencesForEvents(
            $invReport,
            $consolidatedProgramObjectivesMap,
            $eventIds
        );

        // The various objective type are all "Competency Objects" in the context of reporting the curriculum inventory.
        // The are grouped in the "Expectations" section of the report, lump 'em together here.
        $expectations = [];
        $expectations['program_objectives'] = array_filter(
            array_values($programObjectives),
            fn($objective) => ! array_key_exists($objective['id'], $consolidatedProgramObjectivesMap)
        );

        $expectations['session_objectives'] = $sessionObjectives;
        $expectations['course_objectives'] = $courseObjectives;


        // Build out the competency framework information and added to $expectations.
        $pcrs = $this->manager->getPcrs($invReport);

        $pcrsIds = array_keys($pcrs);
        $programObjectiveIds = array_keys($programObjectives);
        $courseObjectiveIds = array_keys($courseObjectives);
        $sessionObjectiveIds = array_keys($sessionObjectives);
        $includes = [
            'pcrs_ids' => [],
            'program_objective_ids' => [],
            'course_objective_ids' => [],
            'session_objective_ids' => [],
        ];
        $relations = [
            'program_objectives_to_pcrs' => [],
            'course_objectives_to_program_objectives' => [],
            'session_objectives_to_course_objectives' => [],
        ];

        $rel = $this->manager->getProgramObjectivesToPcrsRelations(
            $programObjectiveIds,
            $pcrsIds,
            $consolidatedProgramObjectivesMap
        );
        $relations['program_objectives_to_pcrs'] = $rel['relations'];
        $includes['pcrs_ids'] = $rel['pcrs_ids'];
        $includes['program_objective_ids'] = $rel['program_objective_ids'];
        $rel = $this->manager->getCourseObjectivesToProgramObjectivesRelations(
            $courseObjectiveIds,
            $programObjectiveIds,
            $consolidatedProgramObjectivesMap
        );
        $relations['course_objectives_to_program_objectives'] = $rel['relations'];
        $includes['program_objective_ids'] = array_values(
            array_unique(
                array_merge(
                    $includes['program_objective_ids'],
                    $rel['program_objective_ids']
                )
            )
        );
        $includes['course_objective_ids'] = $rel['course_objective_ids'];
        $rel = $this->manager->getSessionObjectivesToCourseObjectivesRelations(
            $sessionObjectiveIds,
            $courseObjectiveIds
        );
        $relations['session_objectives_to_course_objectives'] = $rel['relations'];
        $includes['course_objective_ids'] = array_values(
            array_unique(
                array_merge(
                    $includes['course_objective_ids'],
                    $rel['course_objective_ids']
                )
            )
        );
        $includes['session_objective_ids'] = $rel['session_objective_ids'];

        $expectations['framework'] = [
            'includes' => $includes,
            'relations' => $relations,
        ];

        //
        // transmogrify inventory data for reporting and fill in the blanks
        //
        $events = self::addKeywordsToEvents($events, $keywords);
        $events = self::addResourceTypesToEvents($events, $resourceTypes);
        $events = self::addCompetencyObjectReferencesToEvents($events, $compRefsForEvents);

        //
        // aggregate inventory into single return-array
        //
        $rhett = [];
        $rhett['report'] = $invReport;
        $rhett['expectations'] = $expectations;
        $rhett['institution'] = $institution;
        $rhett['events'] = $events;
        $rhett['sequence_block_references'] = [
            'events' => $eventRefsForSeqBlocks,
            'competency_objects' => $compObjRefsForSeqBlocks,
        ];
        $rhett['institution_domain'] = $this->config->get('institution_domain') ?? '';
        $rhett['supporting_link'] = $this->config->get('supporting_link') ?? '';
        $rhett['created_at'] = time();
        return $rhett;
    }

    /**
     * Returns a lookup map that matches objectives to their most recent siblings, or most recent descendant,
     * within their ancestry tree,
     * excluding the most recent siblings (we don't need to look them up)
     */
    public static function getConsolidatedObjectivesMap(array $objectives): array
    {
        $objectives = array_values($objectives);

        // filter out any objectives without ancestors
        $objectivesWithAncestors = array_filter($objectives, fn($objective) => ! empty($objective['ancestor_id']));

        // sort objectives by ancestor id and by objective id descending,
        // effectively grouping them by ancestor and putting the newest objective in each group on top
        // Obtain a list of columns
        $ids = array_column($objectivesWithAncestors, 'id');
        $ancestorIds = array_column($objectivesWithAncestors, 'ancestor_id');
        array_multisort($ancestorIds, SORT_ASC, $ids, SORT_DESC, $objectivesWithAncestors);

        // map each objective to their most recent/newest sibling in the ancestor tree
        //, excluding the most recent siblings themselves.
        $newestDescendantsMap = [];
        $rhett = [];
        foreach ($objectivesWithAncestors as $objective) {
            $ancestorId = $objective['ancestor_id'];
            $id = $objective['id'];
            if (! array_key_exists($ancestorId, $newestDescendantsMap)) {
                $newestDescendantsMap[$ancestorId] = $id;
                continue;
            }
            $rhett[$id] = $newestDescendantsMap[$ancestorId];
        }

        // make another pass over all objectives.
        // map all ancestors to their most recent descendant.
        foreach ($objectives as $objective) {
            $id = $objective['id'];
            if (array_key_exists($id, $newestDescendantsMap) && ! array_key_exists($id, $rhett)) {
                $newestSiblingId = $newestDescendantsMap[$id];
                $rhett[$id] = $newestSiblingId;
            }
        }

        return $rhett;
    }
}
