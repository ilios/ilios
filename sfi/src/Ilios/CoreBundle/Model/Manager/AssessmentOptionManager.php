<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\AssessmentOptionManagerInterface;
use Ilios\CoreBundle\Entity\AssessmentOptionInterface;

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
