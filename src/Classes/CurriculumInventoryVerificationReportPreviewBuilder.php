<?php

namespace App\Classes;

use App\Entity\CurriculumInventoryReportInterface;
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
     * CurriculumInventoryVerificationReportBuilder constructor.
     *
     * @param Aggregator $aggregator
     */
    public function __construct(Aggregator $aggregator)
    {
        $this->aggregator = $aggregator;
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

        $tables['program-expectations-mapped-to-pcrs'] = $this->getProgramExpectationsMappedToPCRS($data);
        $tables['primary-instructional-methods-by-non-clerkship-sequence-blocks']
            = $this->getPrimaryInstructionalMethodsByNonClerkshipSequenceBlock($data);
        $tables['non-clerkship-sequence-block-instructional-time']
            = $this->getNonClerkshipSequenceBlockInstructionalTime($data);
        $tables['clerkship-sequence-block-instructional-time']
            = $this->getClerkshipSequenceBlockInstructionalTime($data);
        $tables['instructional-method-counts'] = $this->getInstructionalMethodCounts($data);
        $tables['non-clerkship-sequence-block-assessment-methods']
            = $this->getNonClerkshipSequenceBlockAssessmentMethods($data);
        $tables['clerkship-sequence-block-assessment-methods']
            = $this->getClerkshipSequenceBlockAssessmentMethods($data);
        $tables['all-events-with-assessments-tagged-as-formative-or-summative']
            = $this->getAllEventsWithAssessmentsTaggedAsFormativeOrSummative($data);
        $tables['all-resource-types'] = $this->getAllResourceTypes($data);
        return $tables;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function getProgramExpectationsMappedToPCRS(array $data): array
    {
        // @todo implement [ST 2019/08/28]
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function getPrimaryInstructionalMethodsByNonClerkshipSequenceBlock(array $data): array
    {
        // @todo implement [ST 2019/08/28]
    }


    /**
     * @param array $data
     *
     * @return array
     */
    protected function getNonClerkshipSequenceBlockInstructionalTime(array $data): array
    {
        // @todo implement [ST 2019/08/28]
    }


    /**
     * @param array $data
     *
     * @return array
     */
    protected function getClerkshipSequenceBlockInstructionalTime(array $data): array
    {
        // @todo implement [ST 2019/08/28]
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function getInstructionalMethodCounts(array $data): array
    {
        // @todo implement [ST 2019/08/28]
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function getNonClerkshipSequenceBlockAssessmentMethods(array $data): array
    {
        // @todo implement [ST 2019/08/28]
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function getClerkshipSequenceBlockAssessmentMethods(array $data): array
    {
        // @todo implement [ST 2019/08/28]
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function getAllEventsWithAssessmentsTaggedAsFormativeOrSummative(array $data): array
    {
        // @todo implement [ST 2019/08/28]
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function getAllResourceTypes(array $data): array
    {
        // @todo implement [ST 2019/08/28]
    }
}
