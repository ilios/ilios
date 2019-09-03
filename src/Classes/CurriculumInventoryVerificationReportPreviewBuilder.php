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

        $tables['program-expectations-mapped-to-pcrs'] = $this->getProgramExpectationsMappedToPCRS($data);
        $tables['primary-instructional-methods-by-non-clerkship-sequence-blocks']
            = $this->getPrimaryInstructionalMethodsByNonClerkshipSequenceBlock($data);
        $tables['non-clerkship-sequence-block-instructional-time']
            = $this->getNonClerkshipSequenceBlockInstructionalTime($data);
        $tables['clerkship-sequence-block-instructional-time']
            = $this->getClerkshipSequenceBlockInstructionalTime($data);
        $tables['instructional-method-counts']
            = $this->getInstructionalMethodCounts($data, $methodMaps['instructionalMethods']);
        $tables['non-clerkship-sequence-block-assessment-methods']
            = $this->getNonClerkshipSequenceBlockAssessmentMethods($data);
        $tables['clerkship-sequence-block-assessment-methods']
            = $this->getClerkshipSequenceBlockAssessmentMethods($data);
        $tables['all-events-with-assessments-tagged-as-formative-or-summative']
            = $this->getAllEventsWithAssessmentsTaggedAsFormativeOrSummative($data, $methodMaps['assessmentMethods']);
        $tables['all-resource-types'] = $this->getAllResourceTypes($data);
        return $tables;
    }


    /**
     * @return array
     */
    protected function getMethodMaps(): array
    {
        $methodMaps = [
            'instructionalMethods' => [],
            'assessmentMethods' => []
        ];

        $dtos = $this->methodManager->findDTOsBy([]);
        foreach ($dtos as $dto) {
            if (0 === strpos( $dto->id, 'IM')) {
                $methodMaps['instructionalMethods'][$dto->id] = $dto;
            } else {
                $methodMaps['assessmentMethods'][$dto->id] = $dto;

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
                    'programObjectiveId' => $programObjectiveId,
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

        array_multisort(array_column($expectations, 'programObjectiveId'),  SORT_ASC, $expectations);

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
                    'num-events-primary-method' => 0,
                    'num-events-non-primary-method' => 0,
                ];
            }
            $methods[$methodId]['num-events-primary-method']++;
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
                    'num-summative-assessments' => 0,
                    'num-formative-assessments' => 0,
                ];
            }
            if ('summative' === $event['assessment_option_name']) {
                $methods[$methodId]['num-summative-assessments']++;
            } else {
                $methods[$methodId]['num-formative-assessments']++;
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
