<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\LearnerGroupInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class LearnerGroup extends AbstractVoter
{
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof LearnerGroupInterface
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
            self::VIEW => $this->permissionChecker->canViewLearnerGroup($user, $subject->getId()),
            self::CREATE => $this->permissionChecker->canCreateLearnerGroup($user, $subject->getSchool()->getId()),
            self::EDIT => $this->permissionChecker->canUpdateLearnerGroup(
                $user,
                $subject->getSchool()->getId()
            ),
            self::DELETE => $this->permissionChecker->canDeleteLearnerGroup(
                $user,
                $subject->getSchool()->getId()
            ),
            default => false,
        };
    }
}
