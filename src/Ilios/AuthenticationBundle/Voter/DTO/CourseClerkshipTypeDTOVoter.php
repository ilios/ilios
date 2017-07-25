<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\CoreBundle\Entity\DTO\CourseClerkshipTypeDTO;
use Ilios\AuthenticationBundle\Voter\Entity\CourseClerkshipTypeEntityVoter;

/**
 * Class CourseClerkshipTypeDTOVoter
 */
class CourseClerkshipTypeDTOVoter extends CourseClerkshipTypeEntityVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CourseClerkshipTypeDTO && in_array($attribute, [self::VIEW]);
    }
}
