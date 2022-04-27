<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\ProgramYearInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ProgramYear extends AbstractVoter
{
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof ProgramYearInterface
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
            self::EDIT => $this->permissionChecker->canUpdateProgramYear($user, $subject),
            self::CREATE => $this->permissionChecker->canCreateProgramYear($user, $subject->getProgram()),
            self::DELETE => $this->permissionChecker->canDeleteProgramYear($user, $subject),
            self::UNLOCK => $this->permissionChecker->canUnlockProgramYear($user, $subject),
            self::ARCHIVE => $this->permissionChecker->canArchiveProgramYear($user, $subject),
            self::LOCK => $this->permissionChecker->canLockProgramYear($user, $subject),
            default => false,
        };
    }
}
