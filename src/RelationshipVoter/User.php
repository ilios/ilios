<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class User extends AbstractVoter
{
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof UserInterface
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
            self::VIEW => $user->isTheUser($subject) || $user->performsNonLearnerFunction(),
            self::CREATE => $this->permissionChecker->canCreateUser($user, $subject->getSchool()->getId()),
            self::EDIT => $this->permissionChecker->canUpdateUser(
                $user,
                $subject->getSchool()->getId()
            ),
            self::DELETE => $this->permissionChecker->canDeleteUser(
                $user,
                $subject->getSchool()->getId()
            ),
            default => false,
        };
    }
}
