<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Classes\VoterPermissions;
use App\Entity\ProgramYearInterface;
use App\Service\SessionUserPermissionChecker;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ProgramYear extends AbstractVoter
{
    public function __construct(SessionUserPermissionChecker $permissionChecker)
    {
        parent::__construct(
            $permissionChecker,
            ProgramYearInterface::class,
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
            VoterPermissions::EDIT => $this->permissionChecker->canUpdateProgramYear($user, $subject),
            VoterPermissions::CREATE => $this->permissionChecker->canCreateProgramYear($user, $subject->getProgram()),
            VoterPermissions::DELETE => $this->permissionChecker->canDeleteProgramYear($user, $subject),
            VoterPermissions::UNLOCK => $this->permissionChecker->canUnlockProgramYear($user, $subject),
            VoterPermissions::ARCHIVE => $this->permissionChecker->canArchiveProgramYear($user, $subject),
            VoterPermissions::LOCK => $this->permissionChecker->canLockProgramYear($user, $subject),
            default => false,
        };
    }
}
