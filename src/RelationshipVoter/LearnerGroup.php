<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Classes\VoterPermissions;
use App\Entity\LearnerGroupInterface;
use App\Service\SessionUserPermissionChecker;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class LearnerGroup extends AbstractVoter
{
    public function __construct(SessionUserPermissionChecker $permissionChecker)
    {
        parent::__construct(
            $permissionChecker,
            LearnerGroupInterface::class,
            [
                VoterPermissions::CREATE,
                VoterPermissions::VIEW,
                VoterPermissions::EDIT,
                VoterPermissions::DELETE,
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
            VoterPermissions::VIEW => $this->permissionChecker->canViewLearnerGroup($user, $subject->getId()),
            VoterPermissions::CREATE => $this->permissionChecker->canCreateLearnerGroup(
                $user,
                $subject->getSchool()->getId()
            ),
            VoterPermissions::EDIT => $this->permissionChecker->canUpdateLearnerGroup(
                $user,
                $subject->getSchool()->getId()
            ),
            VoterPermissions::DELETE => $this->permissionChecker->canDeleteLearnerGroup(
                $user,
                $subject->getSchool()->getId()
            ),
            default => false,
        };
    }
}
