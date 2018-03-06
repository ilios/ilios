<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\CourseInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Course extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CourseInterface
            && in_array(
                $attribute,
                [self::CREATE, self::VIEW, self::EDIT, self::DELETE, self::UNLOCK, self::UNARCHIVE]
            );
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }
        if ($user->isRoot()) {
            return true;
        }

        switch ($attribute) {
            case self::VIEW:
                return true;
                break;
            case self::CREATE:
                return $this->permissionChecker->canCreateCourse($user, $subject->getSchool());
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateCourse($user, $subject);
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteCourse($user, $subject);
                break;
            case self::UNLOCK:
                return $this->permissionChecker->canUnlockCourse($user, $subject);
                break;
            case self::UNARCHIVE:
                return $this->permissionChecker->canUnarchiveCourse($user, $subject);
                break;
        }

        return false;
    }
}
