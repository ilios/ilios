<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CourseClerkshipTypeInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CourseClerkshipTypeVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CourseClerkshipTypeVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\CourseClerkshipTypeInterface');
    }

    /**
     * @param string $attribute
     * @param CourseClerkshipTypeInterface $type
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $type, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return true;
                break;
            case self::EDIT:
            case self::DELETE:
                return $this->userHasRole($user, ['Developer']);
                break;
        }

        return false;
    }
}
