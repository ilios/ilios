<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CourseVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CourseVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\CourseInterface');
    }

    /**
     * @param string $attribute
     * @param CourseInterface $course
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $course, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                if ($course->getOwningSchool()->getId() === $user->getPrimarySchool()->getId()) {
                    return true;
                }
                break;
            case self::EDIT:
            case self::DELETE:
                if ($course->getOwningSchool()->getId() === $user->getPrimarySchool()->getId()) {
                    return $this->userHasRole($user, ['Course Director', 'Developer', 'Faculty']);
                }
                break;
        }

        return false;
    }
}
