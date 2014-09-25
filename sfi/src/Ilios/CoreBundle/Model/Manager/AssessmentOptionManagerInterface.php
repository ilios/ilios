<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\AssessmentOptionInterface;

/**
 * Interface AssessmentOptionManagerInterface
 */
interface AssessmentOptionManagerInterface
{
    /** 
     *@return AssessmentOptionInterface
     */
    public function createAssessmentOption();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return AssessmentOptionInterface
     */
    public function findAssessmentOptionBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return AssessmentOptionInterface[]|Collection
     */
    public function findAssessmentOptionsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param AssessmentOptionInterface $assessmentOption
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateAssessmentOption(AssessmentOptionInterface $assessmentOption, $andFlush = true);

    /**
     * @param AssessmentOptionInterface $assessmentOption
     *
     * @return void
     */
    public function deleteAssessmentOption(AssessmentOptionInterface $assessmentOption);

    /**
     * @return string
     */
    public function getClass();
}
