<?php

namespace App\Classes;

use App\Entity\CurriculumInventoryReportInterface;
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
            if (0 === strpos( $dto->id, 'IM')) {
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
            // @todo deal with duplicates here via ancestor id. [ST 2019/08/29]
            $pcrsId = $relation['rel2'];
            if (! array_key_exists($programObjectiveId, $expectations)) {
                $expectations[$programObjectiveId] = [
                    'program_objective_id' => $programObjectiveId,
                    'title' => $programObjectivesMap[$programObjectiveId]['title'],
                    'pcrs' => []
                ];
            }
            $expectations[$programObjectiveId]['pcrs'][]
                = str_replace('aamc-pcrs-comp-', '', $pcrsId) . ': ' . $pcrsMap[$pcrsId]->description;
        }

        array_walk($expectations, function(&$expectation) {
           sort($expectation['pcrs']);
        });

        array_multisort(array_column($expectations, 'program_objective_id'),  SORT_ASC, $expectations);

        return $expectations;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function getPrimaryInstructionalMethodsByNonClerkshipSequenceBlock(array $data): array
    {
        // @todo implement [ST 2019/08/28]
        return [];
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
        array_multisort(array_column($methods, 'id'),  SORT_ASC, $methods);
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
        foreach($data['events'] as $event) {
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
        array_multisort(array_column($resources, 'id'),  SORT_ASC, $resources);
        return $resources;
    }
}
