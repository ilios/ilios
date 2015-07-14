<?php
namespace Ilios\CoreBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class LearningMaterialClassConstraint
 * @package Ilios\CoreBundle\Validator\Constraint
 *
 * @Annotation
 */
class LearningMaterialConstraint extends Constraint
{

    /**
     * @inheritdoc
     */
    public function validatedBy()
    {
        return get_class($this) . 'Validator';
    }

    /**
     * @inheritdoc
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}