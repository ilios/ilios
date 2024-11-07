<?php

declare(strict_types=1);

namespace App\Service\CurriculumInventory;

use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\CurriculumInventorySequenceBlockInterface;
use App\Repository\AamcMethodRepository;
use App\Repository\AamcPcrsRepository;
use App\Service\CurriculumInventory\Export\Aggregator;
use Exception;

/**
 * AAMC Verification Report Preview Builder.
 * @package App\Service
 *
 * @link https://www.aamc.org/download/458016/data/lcmetomethodsmap.pdf
 */
class VerificationPreviewBuilder
{
    /**
     * @var array
     */
    private const array TABLE2_METHOD_MAP = [
        'Lecture' => ['IM013'],
        'Lab' => ['IM012'],
        'Small group' => ['IM008', 'IM019', 'IM026'],
        'Patient Contact' => ['IM002', 'IM003', 'IM018', 'IM024', 'IM025', 'IM029'],
        'Other' => [
            'IM001',
            'IM004',
            'IM005',
            'IM006',
            'IM007',
            'IM009',
            'IM010',
            'IM011',
            'IM014',
            'IM015',
            'IM016',
            'IM017',
            'IM020',
            'IM021',
            'IM022',
            'IM023',
            'IM027',
            'IM028',
            'IM030',
            'IM031',
        ],
    ];

    /**
     * @var array
     */
    private const array TABLE5_METHOD_MAP = [
        'Internal exams' => ['AM004', 'AM005'],
        'Lab or practical exams' => ['AM019'],
        'NBME subject exams' => ['AM008'],
        'OSCE / SP exam' => ['AM003'],
        'Faculty / resident rating' => ['AM001', 'AM002', 'AM009', 'AM010', 'AM012', 'AM018'],
        'Paper or oral pres.' => ['AM011', 'AM014', 'AM016'],
        'Other' => ['AM006', 'AM007', 'AM013', 'AM017'],
    ];

    /**
     * @var array
     */
    private const array TABLE6_METHOD_MAP = [
        'NBME subject exams' => ['AM008'],
        'Internal written exams' => ['AM004'],
        'Oral Exam or Pres.' => ['AM005', 'AM011'],
        'Faculty / resident rating' => ['AM001', 'AM002', 'AM009', 'AM010', 'AM012', 'AM018'],
        'OSCE / SP exam' => ['AM003'],
        'Other' => ['AM006', 'AM007', 'AM013', 'AM014', 'AM016', 'AM017', 'AM019'],
    ];

    /**
     * @var null|array
     */
    protected ?array $methodMaps = null;

    /**
     * CurriculumInventoryVerificationReportBuilder constructor.
     */
    public function __construct(
        protected Aggregator $aggregator,
        protected AamcMethodRepository $methodRepository,
        protected AamcPcrsRepository $pcrsRepository
    ) {
    }

    /**
     * @throws Exception
     */
    public function build(CurriculumInventoryReportInterface $report): array
    {
        $tables = [];
        $data = $this->aggregator->getData($report);

        $tables['program_expectations_mapped_to_pcrs'] = $this->getProgramExpectationsMappedToPcrs($data);
        $tables['primary_instructional_methods_by_non_clerkship_sequence_blocks']
            = $this->getPrimaryInstructionalMethodsByNonClerkshipSequenceBlock($data);
        $tables['non_clerkship_sequence_block_instructional_time']
            = $this->getNonClerkshipSequenceBlockInstructionalTime($data);
        $tables['clerkship_sequence_block_instructional_time']
            = $this->getClerkshipSequenceBlockInstructionalTime($data);
        $tables['instructional_method_counts']
            = $this->getInstructionalMethodCounts($data);
        $tables['non_clerkship_sequence_block_assessment_methods']
            = $this->getNonClerkshipSequenceBlockAssessmentMethods($data);
        $tables['clerkship_sequence_block_assessment_methods']
            = $this->getClerkshipSequenceBlockAssessmentMethods($data);
        $tables['all_events_with_assessments_tagged_as_formative_or_summative']
            = $this->getAllEventsWithAssessmentsTaggedAsFormativeOrSummative($data);
        $tables['all_resource_types'] = $this->getAllResourceTypes($data);
        return $tables;
    }

    /**
     * Table 1: Program Expectations Mapped to PCRS
     *
     * Program Expectations are your written learning objectives which learners achieve by graduation from the academic
     * program. The Physician Competency Reference Set (PCRS) is a set of core competencies which may act as a model
     * set of program expectations; relating your Program Expectations to the PCRS is necessary for AAMC to report
     * school program expectations in aggregate. A single Program Expectation can be related to multiple PCRS, and
     * a single PCRS can be related to multiple Program Expectations.
     *
     */
    public function getProgramExpectationsMappedToPcrs(array $data): array
    {
        $dtos = $this->pcrsRepository->findDTOsBy([]);
        $pcrsMap = [];
        foreach ($dtos as $dto) {
            $pcrsMap[$dto->id] = $dto;
        }
        $programObjectivesMap = [];
        foreach ($data['expectations']['program_objectives'] as $programObjective) {
            $programObjectivesMap[$programObjective['id']] = $programObjective;
        }

        $expectations = [];
        foreach ($data['expectations']['framework']['relations']['program_objectives_to_pcrs'] as $relation) {
            $programObjectiveId = $relation['rel1'];
            $pcrsId = $relation['rel2'];
            if (! array_key_exists($programObjectiveId, $expectations)) {
                $title = $programObjectivesMap[$programObjectiveId]['title'];
                $expectations[$programObjectiveId] = [
                    'title' => $title,
                    'title_plain' => trim(strip_tags($title)),
                    'pcrs' => [],
                ];
            }
            $expectations[$programObjectiveId]['pcrs'][]
                = str_replace('aamc-pcrs-comp-', '', $pcrsId) . ': ' . $pcrsMap[$pcrsId]->description;
        }

        array_walk($expectations, function (&$expectation): void {
            sort($expectation['pcrs']);
        });

        // de-dupe
        $hashes = [];
        $dedupedExpectations = [];
        foreach ($expectations as $expectation) {
            $hash = $expectation['title_plain'] . ' || ' . implode(' || ', $expectation['pcrs']);
            if (! in_array($hash, $hashes)) {
                $dedupedExpectations[] = $expectation;
                $hashes[] = $hash;
            }
        }

        $expectations = $dedupedExpectations;

        array_multisort(array_column($expectations, 'title_plain'), SORT_ASC, $expectations);

        array_walk($expectations, function (&$expectation): void {
            unset($expectation['title_plain']);
        });

        return $expectations;
    }

    /**
     * Table 2: Primary Instructional Method by Non-Clerkship Sequence Block
     *
     * Table 2 may be used to support your response to the LCME DCI Tables 6.0-1 and 6.0-2.
     *
     * Primary instructional method refers to whichever instructional method you mark as primary for a given event.
     * If you list more than one instructional method for a given event, the total hours for the event will be
     * attributed to the instructional method marked as primary. For example, if you have a two-and-a-half hour
     * event in your curriculum management system that includes lecture and simulation, and you choose to mark
     * lecture as the primary instructional method, the full 2.5 hours of the event will be attributed to lecture
     * in the Number of Formal Instructional Hours Per Course section of the table below.
     *
     */
    public function getPrimaryInstructionalMethodsByNonClerkshipSequenceBlock(array $data): array
    {
        /** @var CurriculumInventoryReportInterface $report */
        $report = $data['report'];
        $events = $data['events'];
        $rows = [];
        $methods = [];
        $eventRefs = $data['sequence_block_references']['events'];

        $methodsToGroups = $this->getReverseLookupMap(self::TABLE2_METHOD_MAP);

        /** @var CurriculumInventorySequenceBlockInterface $sequenceBlock */
        foreach ($report->getSequenceBlocks()->toArray() as $sequenceBlock) {
            $blockId = $sequenceBlock->getId();
            $course = $sequenceBlock->getCourse();

            if (empty($course)) {
                continue;
            }
            // check if linked course is a clerkship. if not, move on.
            if (! empty($course->getClerkshipType())) {
                continue;
            }

            if (! array_key_exists($blockId, $eventRefs)) {
                continue;
            }

            $row = [
                'title' => $sequenceBlock->getTitle(),
                'starting_level' => $sequenceBlock->getStartingAcademicLevel()->getLevel(),
                'ending_level' => $sequenceBlock->getEndingAcademicLevel()->getLevel(),
                'instructional_methods' => [],
                'total' => 0,
            ];

            foreach ($eventRefs[$blockId] as $eventRef) {
                $event = $events[$eventRef['event_id']];
                $methodId = $event['method_id'];
                if (str_starts_with($methodId, 'IM')) {
                    $groups = $methodsToGroups[$methodId];
                    foreach ($groups as $group) {
                        if (! array_key_exists($group, $row['instructional_methods'])) {
                            $row['instructional_methods'][$group] = 0;
                        }
                        $row['instructional_methods'][$group] += $event['duration'];
                        $row['total'] += $event['duration'];

                        if (! array_key_exists($group, $methods)) {
                            $methods[$group] = [
                                'title' => $group,
                                'total' => 0,
                            ];
                        }
                        $methods[$group]['total'] += $event['duration'];
                    }
                }
            }
            $rows[] = $row;
        }
        $methods = array_values($methods);
        array_multisort(array_column($methods, 'title'), SORT_ASC, $methods);
        array_multisort(
            array_column($rows, 'starting_level'),
            SORT_ASC,
            array_column($rows, 'ending_level'),
            SORT_ASC,
            array_column($rows, 'title'),
            SORT_ASC,
            $rows
        );

        return ['methods' => $methods, 'rows' => $rows];
    }

    /**
     * Table 3-A: Non-Clerkship Sequence Block Instructional Time
     *
     * The amount of time (Total Weeks) in a Sequence Block is calculated using duration (the number of days divided
     * into 5-day weeks). For example, a 75-day duration Sequence Block will be calculated as 15 weeks.
     * Duration is an optional field for non-clerkship sequence blocks and is provided by you.
     * Only Sequence Blocks with duration values provided will appear in the table below; if you do not see any
     * Sequence Blocks below, it means you have not provided any durations for non-clerkship Sequence Blocks.
     * The Average Hours of Instruction Per Week is calculated by summing the total event duration (hours, minutes)
     * for events tagged with an instructional method divided by the number of weeks in the Sequence Block
     * (as calculated above).
     * This table displays events that are tagged with only instructional methods, and events that are tagged with
     * both instructional methods and assessment methods.
     * It does not include events that are tagged with only assessment methods (i.e., Assessment Events).
     *
     */
    public function getNonClerkshipSequenceBlockInstructionalTime(array $data): array
    {
        return $this->getSequenceBlockInstructionalTime($data, false);
    }

    /**
     * Table 3-B: Clerkship Sequence Block Instructional Time
     *
     * Table 3-B may be used to support your response to the LCME DCI Table 6.0-3.
     *
     * The amount of time (Total Weeks) in a Clerkship Sequence Block is calculated using duration (the number of days
     * divided into 5-day weeks).
     * For example, a 75-day duration Clerkship Sequence Block will be calculated as 15 weeks.
     * Duration is a required field for clerkship sequence blocks and is provided by you.
     * The Average Hours of Instruction Per Week is calculated by summing the total event duration (hours, minutes)
     * for events tagged with an instructional method divided by the number of weeks in the Clerkship Sequence Block.
     * This table displays events that are tagged with only instructional methods, and events that are tagged with
     * both instructional methods and assessment methods.
     * It does not include events that are tagged with only assessment methods (i.e., Assessment Events).
     *
     */
    public function getClerkshipSequenceBlockInstructionalTime(array $data): array
    {
        return $this->getSequenceBlockInstructionalTime($data, true);
    }

    /**
     * Table 4: Instructional Method Counts
     *
     * This table shows the number of times each Instructional Method was used; this differs from Table 2 which groups
     * some instructional methods. The CI Standardized Vocabulary contains definitions of all Instructional Methods.
     * Each primary instructional method is counted in the Number of Events Featuring This as the Primary Method column;
     * events used in multiple sequence blocks will only have their instructional methods counted once in this table
     * so the sum reflects total number of instructional events in your curriculum.
     * Each occurrence of an instructional method that is not indicated as the Primary Method will be tallied in
     * the Number of Non-primary Occurrences of This Method.
     * If an instructional method is tagged more than once as non-primary to a given event, each occurrence of the
     * instructional method is counted.
     *
     */
    public function getInstructionalMethodCounts(array $data): array
    {
        $instructionalMethodsById = $this->getMethodMaps()['instructional_methods'];
        $methods = [];
        foreach ($data['events'] as $event) {
            $methodId = $event['method_id'];
            if (! array_key_exists($methodId, $instructionalMethodsById)) {
                continue;
            }
            if (! array_key_exists($methodId, $methods)) {
                $methods[$methodId] = [
                    'id' => $methodId,
                    'title' => $instructionalMethodsById[$methodId]->description,
                    'num_events_primary_method' => 0,
                    'num_events_non_primary_method' => 0,
                ];
            }
            $methods[$methodId]['num_events_primary_method']++;
        }

        $methods = array_values($methods);
        array_multisort(array_column($methods, 'id'), SORT_ASC, $methods);
        return $methods;
    }

    /**
     * Table 5: Non-Clerkship Sequence Block Assessment Methods
     *
     * Table 5 may be used to support your response to the LCME DCI Tables 9.0-1 and 9.0-2.
     *
     * Every Sequence Block that is not a clerkship and contains at least one Assessment Event will appear in this
     * table. An Assessment Event may contain more than one assessment method.; Sequence Blocks that do not have
     * Assessment Events will not appear - events that are tagged with both instructional methods and assessment methods
     * do not appear in this table.
     *
     * Select assessment methods are grouped to assist in completing the LCME DCI.
     * Number of exams is calculated by totaling the number Assessment Events in a sequence block that have assessment
     * methods tagged as summative.
     * When a specific assessment method is employed in a Sequence Block, the corresponding grouping will get an X.
     * If at least one assessment method in an event in the Sequence Block is tagged as formative,
     * the Formative Assessment (Y/N) column will get a Y.
     * If at least one event in the Sequence Block contains AM010, Narrative Assessment Narrative Assessment (Y/N)
     * will get a Y.
     *
     */
    public function getNonClerkshipSequenceBlockAssessmentMethods(array $data): array
    {
        return $this->getSequenceBlockAssessmentMethods($data, self::TABLE5_METHOD_MAP, false);
    }

    /**
     * Table 6: Clerkship Sequence Block Assessment Methods
     *
     * Table 6 may be used to support your response to the LCME DCI Table 9.0-3.
     *
     * Every Sequence Block that is a clerkship and contains one Assessment Event will appear in this table;
     * Sequence Blocks that do not have Assessment Events will not appear - events that are tagged with both
     * instructional methods and assessment methods do not appear in this table.
     *
     * Select assessment methods are grouped to assist in completing the LCME DCI.
     * Number of exams is calculated by totaling the number Assessment Events in a sequence block that have assessment
     * methods tagged as summative.
     * When a specific assessment method is employed in a Sequence Block, the corresponding grouping will get an X.
     * If at least one assessment method in an event in the Sequence Block is tagged as formative,
     * the Formative Assessment (Y/N) column will get a Y.
     * If at least one event in the Sequence Block contains AM010, Narrative Assessment, the Narrative Assessment (Y/N)
     * will get a Y.
     *
     */
    public function getClerkshipSequenceBlockAssessmentMethods(array $data): array
    {
        return $this->getSequenceBlockAssessmentMethods($data, self::TABLE6_METHOD_MAP, true);
    }

    /**
     * Table 7: All Events with Assessments Tagged as Formative or Summative
     *
     * This table counts all assessment methods tagged within events.
     * The CI Standardized Vocabulary contains definitions of all assessment methods. All assessment methods must be
     * tagged as formative or summative.
     * If you tag a single event with the same assessment method (either both formative, both summative, or one
     * formative and one summative), they will both be counted.
     * A single event used in multiple sequence blocks will only be counted once in this table.
     * The Number of Summative Assessments, when summed, represents the total number of assessment methods tagged
     * as summative in your curriculum; the Number of Formative Assessments, when summed, represents the total number
     * of assessment methods tagged as formative in your curriculum.
     *
     */
    public function getAllEventsWithAssessmentsTaggedAsFormativeOrSummative(array $data): array
    {
        $assessmentMethodsById = $this->getMethodMaps()['assessment_methods'];
        $methods = [];
        foreach ($data['events'] as $event) {
            $methodId = $event['method_id'];
            if (! array_key_exists($methodId, $assessmentMethodsById)) {
                continue;
            }
            if (! array_key_exists($methodId, $methods)) {
                $methods[$methodId] = [
                    'id' => $methodId,
                    'title' => $assessmentMethodsById[$methodId]->description,
                    'num_summative_assessments' => 0,
                    'num_formative_assessments' => 0,
                ];
            }
            if ('summative' === $event['assessment_option_name']) {
                $methods[$methodId]['num_summative_assessments']++;
            } else {
                $methods[$methodId]['num_formative_assessments']++;
            }
        }

        $methods = array_values($methods);
        array_multisort(array_column($methods, 'id'), SORT_ASC, $methods);
        return $methods;
    }

    /**
     * Table 8: All Resource Types
     *
     * All resources tagged in each event are counted, including multiple occurrences of the same resource within a
     * single event.
     *
     */
    public function getAllResourceTypes(array $data): array
    {
        $resources = [];
        foreach ($data['events'] as $event) {
            if (array_key_exists('resource_types', $event)) {
                foreach ($event['resource_types'] as $resourceType) {
                    $resourceTypeId = $resourceType['resource_type_id'];
                    if (! array_key_exists($resourceTypeId, $resources)) {
                        $resources[$resourceTypeId] = [
                            'id' => $resourceTypeId,
                            'title' => $resourceType['resource_type_title'],
                            'count' => 0,
                        ];
                    }
                    $resources[$resourceTypeId]['count']++;
                }
            }
        }
        $resources = array_values($resources);
        array_multisort(array_column($resources, 'id'), SORT_ASC, $resources);
        return $resources;
    }

    protected function getReverseLookupMap(array $map): array
    {
        $reverseMap = [];
        foreach ($map as $key => $values) {
            foreach ($values as $value) {
                if (! array_key_exists($value, $reverseMap)) {
                    $reverseMap[$value] = [];
                }
                $reverseMap[$value][] = $key;
            }
        }
        return $reverseMap;
    }

    protected function getSequenceBlockAssessmentMethods(array $data, array $map, bool $clerkships = false): array
    {
        $rows = [];
        $methods = array_keys($map);
        sort($methods);
        $eventRefs = $data['sequence_block_references']['events'];
        $events = $data['events'];

        $methodsToGroups = $this->getReverseLookupMap($map);

        /** @var CurriculumInventoryReportInterface $report */
        $report = $data['report'];
        /** @var CurriculumInventorySequenceBlockInterface $sequenceBlock */
        foreach ($report->getSequenceBlocks()->toArray() as $sequenceBlock) {
            $blockId = $sequenceBlock->getId();
            $course = $sequenceBlock->getCourse();
            if (! $course) {
                continue;
            }
            $clerkshipType = $course->getClerkshipType();
            if ($clerkships === empty($clerkshipType)) {
                continue;
            }

            $row = [
                'title' => $sequenceBlock->getTitle(),
                'starting_level' => $sequenceBlock->getStartingAcademicLevel()->getLevel(),
                'ending_level' => $sequenceBlock->getEndingAcademicLevel()->getLevel(),
                'methods' => array_fill_keys($methods, false),
                'num_exams' => 0,
                'has_formative_assessments' => false,
                'has_narrative_assessments' => false,
            ];
            $hasAssessmentMethods = false;

            if (array_key_exists($blockId, $eventRefs)) {
                foreach ($eventRefs[$blockId] as $eventRef) {
                    $event = $events[$eventRef['event_id']];
                    $methodId = $event['method_id'];
                    if (str_starts_with($methodId, 'AM')) {
                        $hasAssessmentMethods = true;

                        if ('formative' === $event['assessment_option_name']) {
                            $row['has_formative_assessments'] = true;
                        }
                        if ('AM010' === $methodId) {
                            $row['has_narrative_assessments'] = true;
                        }

                        if ('summative' === $event['assessment_option_name']) {
                            $row['num_exams']++;
                        }

                        if (array_key_exists($methodId, $methodsToGroups)) {
                            foreach ($methodsToGroups[$methodId] as $method) {
                                $row['methods'][$method] = true;
                            }
                        }
                    }
                }
            }
            if ($hasAssessmentMethods) {
                $rows[] = $row;
            }
        }
        array_multisort(
            array_column($rows, 'starting_level'),
            SORT_ASC,
            array_column($rows, 'ending_level'),
            SORT_ASC,
            array_column($rows, 'title'),
            SORT_ASC,
            $rows
        );
        return ['methods' => $methods, 'rows' => $rows];
    }

    protected function getMethodMaps(): array
    {
        if (! is_null($this->methodMaps)) {
            return $this->methodMaps;
        }

        $this->methodMaps = [
            'instructional_methods' => [],
            'assessment_methods' => [],
        ];

        $dtos = $this->methodRepository->findDTOsBy([]);
        foreach ($dtos as $dto) {
            if (str_starts_with($dto->id, 'IM')) {
                $this->methodMaps['instructional_methods'][$dto->id] = $dto;
            } else {
                $this->methodMaps['assessment_methods'][$dto->id] = $dto;
            }
        }
        return $this->methodMaps;
    }

    protected function getSequenceBlockInstructionalTime(array $data, bool $clerkships = false): array
    {
        /** @var CurriculumInventoryReportInterface $report */
        $report = $data['report'];
        $events = $data['events'];
        $rows = [];
        $eventRefs = $data['sequence_block_references']['events'];

        /** @var CurriculumInventorySequenceBlockInterface $sequenceBlock */
        foreach ($report->getSequenceBlocks()->toArray() as $sequenceBlock) {
            $blockId = $sequenceBlock->getId();
            $course  = $sequenceBlock->getCourse();
            $duration = $sequenceBlock->getDuration();

            if (! $duration) {
                continue;
            }

            if (empty($course)) {
                continue;
            }

            if ($clerkships === empty($course->getClerkshipType())) {
                continue;
            }

            $weeks = round($duration / 5, 2);
            $time = 0;
            if (array_key_exists($blockId, $eventRefs)) {
                foreach ($eventRefs[$blockId] as $eventRef) {
                    $event = $events[$eventRef['event_id']];
                    $methodId = $event['method_id'];
                    if (str_starts_with($methodId, 'IM')) {
                        $time += $event['duration'];
                    }
                }
            }

            $rows[] = [
                'title' => $sequenceBlock->getTitle(),
                'starting_level' => $sequenceBlock->getStartingAcademicLevel()->getLevel(),
                'ending_level' => $sequenceBlock->getEndingAcademicLevel()->getLevel(),
                'weeks' => $weeks,
                'avg' => round(($time / 60) / ($duration / 5), 2),
            ];
        }

        array_multisort(
            array_column($rows, 'starting_level'),
            SORT_ASC,
            array_column($rows, 'ending_level'),
            SORT_ASC,
            array_column($rows, 'title'),
            SORT_ASC,
            $rows
        );

        return $rows;
    }
}
