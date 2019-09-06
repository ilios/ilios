<?php

namespace App\Classes;

use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\CurriculumInventorySequenceBlockInterface;
use App\Entity\Manager\AamcMethodManager;
use App\Entity\Manager\AamcPcrsManager;
use App\Service\CurriculumInventory\Export\Aggregator;
use Exception;

/**
 * Class CurriculumInventoryVerificationReportBuilder
 * @package App\Classes
 */
class CurriculumInventoryVerificationReportPreviewBuilder
{
    /**
     * @var array
     */
    const INSTRUCTIONAL_METHOD_GROUPS = [
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
            'IM031'
        ],
    ];

    /**
     * @var array
     */
    const ASSESSMENT_METHOD_GROUPS = [
        'Internal exams' => ['AM004', 'AM005'],
        'Lab or practical exams' => ['AM019'],
        'NBME subject exams' => ['AM008'],
        'Faculty/ resident rating' => ['AM001', 'AM002', 'AM009', 'AM010', 'AM012', 'AM018'],
        'OSCE/SP exam' => ['AM003'],
        'Other' => ['AM006', 'AM007', 'AM013', 'AM014', 'AM016', 'AM017', 'AM019']
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
        $methodMaps = $this->getMethodMaps();

        $tables['program_expectations_mapped_to_pcrs'] = $this->getProgramExpectationsMappedToPCRS($data);
        $tables['primary_instructional_methods_by_non_clerkship_sequence_blocks']
            = $this->getPrimaryInstructionalMethodsByNonClerkshipSequenceBlock($data);
        $tables['non_clerkship_sequence_block_instructional_time']
            = $this->getNonClerkshipSequenceBlockInstructionalTime($data);
        $tables['clerkship_sequence_block_instructional_time']
            = $this->getClerkshipSequenceBlockInstructionalTime($data);
        $tables['instructional_method_counts']
            = $this->getInstructionalMethodCounts($data, $methodMaps['instructional_methods']);
        $tables['non_clerkship_sequence_block_assessment_methods']
            = $this->getNonClerkshipSequenceBlockAssessmentMethods($data);
        $tables['clerkship_sequence_block_assessment_methods']
            = $this->getClerkshipSequenceBlockAssessmentMethods($data);
        $tables['all_events_with_assessments_tagged_as_formative_or_summative']
            = $this->getAllEventsWithAssessmentsTaggedAsFormativeOrSummative($data, $methodMaps['assessment_methods']);
        $tables['all_resource_types'] = $this->getAllResourceTypes($data);
        return $tables;
    }


    /**
     * @return array
     */
    protected function getMethodMaps(): array
    {
        $methodMaps = [
            'instructional_methods' => [],
            'assessment_methods' => []
        ];

        $dtos = $this->methodManager->findDTOsBy([]);
        foreach ($dtos as $dto) {
            if (0 === strpos($dto->id, 'IM')) {
                $methodMaps['instructional_methods'][$dto->id] = $dto;
            } else {
                $methodMaps['assessment_methods'][$dto->id] = $dto;
            }
        }
        return $methodMaps;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function getProgramExpectationsMappedToPCRS(array $data): array
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
                    'pcrs' => []
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
    protected function getPrimaryInstructionalMethodsByNonClerkshipSequenceBlock(array $data): array
    {
        /* @var CurriculumInventoryReportInterface $report */
        $report = $data['report'];
        $events = $data['events'];
        $rows = [];
        $methods = [];
        $eventRefs = $data['sequence_block_references']['events'];

        $methodsToGroups = $this->getReverseLookupMap(self::INSTRUCTIONAL_METHOD_GROUPS);

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

        return ['methods' => $methods, 'clerkships' => $rows];
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function getNonClerkshipSequenceBlockInstructionalTime(array $data): array
    {
        // @todo implement [ST 2019/08/28]
        return [];
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function getClerkshipSequenceBlockInstructionalTime(array $data): array
    {
        // @todo implement [ST 2019/08/28]
        return [];
    }

    /**
     * @param array $data
     * @param array $instructionalMethodsById
     *
     * @return array
     */
    protected function getInstructionalMethodCounts(array $data, array $instructionalMethodsById): array
    {
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
    protected function getNonClerkshipSequenceBlockAssessmentMethods(array $data): array
    {
        $methods = [];
        // @todo implement [ST 2019/08/28]
        return [];
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function getClerkshipSequenceBlockAssessmentMethods(array $data): array
    {
        // @todo implement [ST 2019/08/28]
        return [];
    }

    /**
     * @param array $data
     * @param array $assessmentMethodsById
     *
     * @return array
     */
    protected function getAllEventsWithAssessmentsTaggedAsFormativeOrSummative(
        array $data,
        array $assessmentMethodsById
    ): array {
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
    protected function getAllResourceTypes(array $data): array
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
                            'count' => 0
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
}
