<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\CourseInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Course extends AbstractVoter
{
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof CourseInterface
            && in_array($attribute, [
                self::CREATE,
                self::VIEW,
                self::EDIT,
                self::DELETE,
                self::UNLOCK,
                self::LOCK,
                self::ARCHIVE,
            ]);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }
        if ($user->isRoot()) {
            return true;
        }
        return match ($attribute) {
            self::VIEW => true,
            self::CREATE => $this->permissionChecker->canCreateCourse($user, $subject->getSchool()),
            self::EDIT => $this->permissionChecker->canUpdateCourse($user, $subject),
            self::DELETE => $this->permissionChecker->canDeleteCourse($user, $subject),
            self::UNLOCK => $this->permissionChecker->canUnlockCourse($user, $subject),
            self::ARCHIVE => $this->permissionChecker->canArchiveCourse($user, $subject),
            self::LOCK => $this->permissionChecker->canLockCourse($user, $subject),
            default => false,
        };
    }
}
