<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\AssessmentOptionManagerInterface;
use Ilios\CoreBundle\Model\AssessmentOptionInterface;

/**
 * AssessmentOptionManager
 */
abstract class AssessmentOptionManager implements AssessmentOptionManagerInterface
{
    /**
    * @return AssessmentOptionInterface
    */
    public function createAssessmentOption()
    {
        $class = $this->getClass();

        return new $class();
    }
}
