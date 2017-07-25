<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\Voter\CourseVoter;
use Ilios\CoreBundle\Entity\CourseInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CourseEntityVoter
 */
class CourseEntityVoter extends CourseVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CourseInterface && in_array($attribute, array(
            self::CREATE, self::VIEW, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param CourseInterface $course
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $course, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->isViewGranted($course->getId(), $course->getSchool()->getId(), $user);
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                return $this->isWriteGranted($course->getId(), $course->getSchool()->getId(), $user);
                break;
        }
        return false;
    }
}
