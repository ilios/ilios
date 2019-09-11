<?php

namespace App\Service\CurriculumInventory;

use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\CurriculumInventorySequenceBlockInterface;
use App\Entity\Manager\AamcMethodManager;
use App\Entity\Manager\AamcPcrsManager;
use App\Service\CurriculumInventory\Export\Aggregator;
use Exception;

/**
 * Class VerificationBuilder
 * @package App\Service
 */
class VerificationPreviewBuilder
{
    /**
     * @var array
     */
    const TABLE2_METHOD_MAP = [
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
    const TABLE5_METHOD_MAP = [
        'Internal exams' => ['AM004', 'AM005'],
        'Lab or practical exams' => ['AM019'],
        'NBME subject exams' => ['AM008'],
        'OSCE/SP exam' => ['AM003'],
        'Faculty/resident rating' => ['AM001', 'AM002', 'AM009', 'AM010', 'AM012', 'AM018'],
        'Paper or oral pres.' => ['AM011', 'AM014', 'AM016'],
        'Other' => ['AM006', 'AM007', 'AM013', 'AM017'],
    ];

    /**
     * @var array
     */
    const TABLE6_METHOD_MAP = [
        'NBME subject exams' => ['AM008'],
        'Internal written exams' => ['AM004'],
        'Oral Exam or Pres.' => ['AM005', 'AM011'],
        'Faculty/resident rating' => ['AM001', 'AM002', 'AM009', 'AM010', 'AM012', 'AM018'],
        'OSCE/SP exam' => ['AM003'],
        'Other' => ['AM006', 'AM007', 'AM013', 'AM014', 'AM016', 'AM017', 'AM019'],
    ];

    /**
     * @var Aggregator
     */
    protected $aggregator;

    /**
     * @var AamcMethodManager
     */
    protected $methodManager;

    /**
     * @var AamcPcrsManager
     */
    protected $pcrsManager;

    /**
     * @var null|array
     */
    protected $methodMaps = null;

    /**
     * CurriculumInventoryVerificationReportBuilder constructor.
     *
     * @param Aggregator $aggregator
     * @param AamcMethodManager $methodManager
     * @param AamcPcrsManager $pcrsManager
     */
    public function __construct(Aggregator $aggregator, AamcMethodManager $methodManager, AamcPcrsManager $pcrsManager)
    {
        $this->aggregator = $aggregator;
        $this->methodManager = $methodManager;
        $this->pcrsManager = $pcrsManager;
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @return array
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
     * @param array $data
     *
     * @return array
     */
    public function getProgramExpectationsMappedToPcrs(array $data): array
    {
        $dtos = $this->pcrsManager->findDTOsBy([]);
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
                $expectations[$programObjectiveId] = [
                    'title' => $programObjectivesMap[$programObjectiveId]['title'],
                    'pcrs' => [],
                ];
            }
            $expectations[$programObjectiveId]['pcrs'][]
                = str_replace('aamc-pcrs-comp-', '', $pcrsId) . ': ' . $pcrsMap[$pcrsId]->description;
        }

        array_walk($expectations, function (&$expectation) {
            sort($expectation['pcrs']);
        });

        // de-dupe
        $hashes = [];
        $dedupedExpectations = [];
        foreach ($expectations as $expectation) {
            $hash = $expectation['title'] . ' || ' . implode(' || ', $expectation['pcrs']);
            if (! in_array($hash, $hashes)) {
                $dedupedExpectations[] = $expectation;
                $hashes[] = $hash;
            }
        }

        $expectations = $dedupedExpectations;

        array_multisort(array_column($expectations, 'title'), SORT_ASC, $expectations);

        return $expectations;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function getPrimaryInstructionalMethodsByNonClerkshipSequenceBlock(array $data): array
    {
        /* @var CurriculumInventoryReportInterface $report */
        $report = $data['report'];
        $events = $data['events'];
        $rows = [];
        $methods = [];
        $eventRefs = $data['sequence_block_references']['events'];

        $methodsToGroups = $this->getReverseLookupMap(self::TABLE2_METHOD_MAP);

        /* @var CurriculumInventorySequenceBlockInterface $sequenceBlock */
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
                'level' => $sequenceBlock->getAcademicLevel()->getLevel(),
                'instructional_methods' => [],
                'total' => 0,
            ];

            foreach ($eventRefs[$blockId] as $eventRef) {
                $event = $events[$eventRef['event_id']];
                $methodId = $event['method_id'];
                if (0 === strpos($methodId, 'IM')) {
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
            array_column($rows, 'level'),
            SORT_ASC,
            array_column($rows, 'title'),
            SORT_ASC,
            $rows
        );

        return ['methods' => $methods, 'rows' => $rows];
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function getNonClerkshipSequenceBlockInstructionalTime(array $data): array
    {
        return $this->getSequenceBlockInstructionalTime($data, false);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function getClerkshipSequenceBlockInstructionalTime(array $data): array
    {
        return $this->getSequenceBlockInstructionalTime($data, true);
    }

    /**
     * @param array $data
     * @param bool $clerkships
     *
     * @return array
     */
    protected function getSequenceBlockInstructionalTime(array $data, $clerkships = false): array
    {
        /* @var CurriculumInventoryReportInterface $report */
        $report = $data['report'];
        $events = $data['events'];
        $rows = [];
        $eventRefs = $data['sequence_block_references']['events'];

        /* @var CurriculumInventorySequenceBlockInterface $sequenceBlock */
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
                    if (0 === strpos($methodId, 'IM')) {
                        $time += $event['duration'];
                    }
                }
            }

            $rows[] = [
                'title' => $sequenceBlock->getTitle(),
                'level' => $sequenceBlock->getAcademicLevel()->getLevel(),
                'weeks' => $weeks,
                'avg' => round(($time / 60) / ($duration / 5), 2),
            ];
        }

        array_multisort(
            array_column($rows, 'level'),
            SORT_ASC,
            array_column($rows, 'title'),
            SORT_ASC,
            $rows
        );

        return $rows;
    }

    /**
     * @param array $data
     *
     * @return array
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
     * @param array $data
     *
     * @return array
     */
    public function getNonClerkshipSequenceBlockAssessmentMethods(array $data): array
    {
        return $this->getSequenceBlockAssessmentMethods($data, self::TABLE5_METHOD_MAP, false);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function getClerkshipSequenceBlockAssessmentMethods(array $data): array
    {
        return $this->getSequenceBlockAssessmentMethods($data, self::TABLE6_METHOD_MAP, true);
    }

    /**
     * @param array $data
     *
     * @return array
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
     * @param array $data
     *
     * @return array
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

    /**
     * @param array $map
     * @return array
     */
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

    /**
     * @param array $data
     * @param array $map
     * @param bool $clerkships
     *
     * @return array
     */
    protected function getSequenceBlockAssessmentMethods(array $data, array $map, $clerkships = false): array
    {
        $rows = [];
        $methods = array_keys($map);
        sort($methods);
        $eventRefs = $data['sequence_block_references']['events'];
        $events = $data['events'];

        $methodsToGroups = $this->getReverseLookupMap($map);

        /* @var CurriculumInventoryReportInterface $report */
        $report = $data['report'];
        /* @var CurriculumInventorySequenceBlockInterface $sequenceBlock */
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
                'level' => $sequenceBlock->getAcademicLevel()->getLevel(),
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
                    if (0 === strpos($methodId, 'AM')) {
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
            array_column($rows, 'level'),
            SORT_ASC,
            array_column($rows, 'title'),
            SORT_ASC,
            $rows
        );
        return ['methods' => $methods, 'rows' => $rows];
    }

    /**
     * @return array
     */
    protected function getMethodMaps(): array
    {
        if (! is_null($this->methodMaps)) {
            return $this->methodMaps;
        }

        $this->methodMaps = [
            'instructional_methods' => [],
            'assessment_methods' => [],
        ];

        $dtos = $this->methodManager->findDTOsBy([]);
        foreach ($dtos as $dto) {
            if (0 === strpos($dto->id, 'IM')) {
                $this->methodMaps['instructional_methods'][$dto->id] = $dto;
            } else {
                $this->methodMaps['assessment_methods'][$dto->id] = $dto;
            }
        }
        return $this->methodMaps;
    }
}
