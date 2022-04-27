<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\InstructorGroupInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class InstructorGroup extends AbstractVoter
{
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof InstructorGroupInterface
            && in_array($attribute, [self::CREATE, self::VIEW, self::EDIT, self::DELETE]);
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
            self::CREATE => $this->permissionChecker->canCreateInstructorGroup(
                $user,
                $subject->getSchool()->getId()
            ),
            self::EDIT => $this->permissionChecker->canUpdateInstructorGroup(
                $user,
                $subject->getSchool()->getId()
            ),
            self::DELETE => $this->permissionChecker->canDeleteInstructorGroup(
                $user,
                $subject->getSchool()->getId()
            ),
            default => false,
        };
    }
}
