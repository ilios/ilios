<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\AssessmentOptionInterface;

/**
 * Class AssessmentOptionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class AssessmentOptionManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findAssessmentOptionBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findAssessmentOptionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateAssessmentOption(
        AssessmentOptionInterface $assessmentOption,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($assessmentOption, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteAssessmentOption(
        AssessmentOptionInterface $assessmentOption
    ) {
        $this->delete($assessmentOption);
    }

    /**
     * @deprecated
     */
    public function createAssessmentOption()
    {
        return $this->create();
    }
}
