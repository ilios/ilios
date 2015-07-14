<?php
namespace Ilios\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class IsValidLearningMaterial
 * @package Ilios\CoreBundle\Validator\Constraint
 *
 * @Annotation
 */
class IsValidLearningMaterial extends Constraint
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