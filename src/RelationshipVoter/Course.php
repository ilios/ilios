<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Classes\VoterPermissions;
use App\Entity\CourseInterface;
use App\Service\SessionUserPermissionChecker;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Course extends AbstractVoter
{
    public function __construct(SessionUserPermissionChecker $permissionChecker)
    {
        parent::__construct(
            $permissionChecker,
            CourseInterface::class,
            [
                VoterPermissions::CREATE,
                VoterPermissions::VIEW,
                VoterPermissions::EDIT,
                VoterPermissions::DELETE,
                VoterPermissions::UNLOCK,
                VoterPermissions::LOCK,
                VoterPermissions::ARCHIVE,
            ]
        );
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }
        if ($user->isRoot()) {
            return true;
        }
        return match ($attribute) {
            VoterPermissions::VIEW => true,
            VoterPermissions::CREATE => $this->permissionChecker->canCreateCourse($user, $subject->getSchool()),
            VoterPermissions::EDIT => $this->permissionChecker->canUpdateCourse($user, $subject),
            VoterPermissions::DELETE => $this->permissionChecker->canDeleteCourse($user, $subject),
            VoterPermissions::UNLOCK => $this->permissionChecker->canUnlockCourse($user, $subject),
            VoterPermissions::ARCHIVE => $this->permissionChecker->canArchiveCourse($user, $subject),
            VoterPermissions::LOCK => $this->permissionChecker->canLockCourse($user, $subject),
            default => false,
        };
    }
}
