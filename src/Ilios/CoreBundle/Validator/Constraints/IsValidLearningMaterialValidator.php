<?php

namespace Ilios\CoreBundle\Validator\Constraints;

use Ilios\CoreBundle\Entity\LearningMaterial;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class IsValidLearningMaterialValidator
 * @package Ilios\CoreBundle\Validator\Constraints
 */
class IsValidLearningMaterialValidator extends ConstraintValidator
{
    /**
     * Checks if the passed learning material is valid.
     *
     * @param LearningMaterial $learningMaterial The learning material that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($learningMaterial, Constraint $constraint)
    {
        // TODO: Implement validate() method.
    }
}
