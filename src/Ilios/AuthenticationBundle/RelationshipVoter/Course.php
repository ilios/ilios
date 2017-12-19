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
                [self::CREATE, self::VIEW, self::EDIT, self::DELETE]
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

        if ($subject instanceof CourseInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
    }

    protected function voteOnEntity(string $attribute, SessionUserInterface $sessionUser, CourseInterface $course): bool
    {
        switch ($attribute) {
            case self::VIEW:
                return $this->permissionChecker->canReadCourse(
                    $sessionUser,
                    $course->getId(),
                    $course->getSchool()->getId()
                );
                break;
            case self::CREATE:
                return $this->permissionChecker->canCreateCourse($sessionUser, $course->getSchool()->getId());
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateCourse(
                    $sessionUser,
                    $course->getId(),
                    $course->getSchool()->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteCourse(
                    $sessionUser,
                    $course->getId(),
                    $course->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
