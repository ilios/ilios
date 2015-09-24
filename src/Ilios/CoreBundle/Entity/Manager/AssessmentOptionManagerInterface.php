<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\AssessmentOptionInterface;

/**
 * Interface AssessmentOptionManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface AssessmentOptionManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return AssessmentOptionInterface
     */
    public function findAssessmentOptionBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return AssessmentOptionInterface[]
     */
    public function findAssessmentOptionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param AssessmentOptionInterface $assessmentOption
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateAssessmentOption(
        AssessmentOptionInterface $assessmentOption,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param AssessmentOptionInterface $assessmentOption
     *
     * @return void
     */
    public function deleteAssessmentOption(
        AssessmentOptionInterface $assessmentOption
    );

    /**
     * @return AssessmentOptionInterface
     */
    public function createAssessmentOption();
}
