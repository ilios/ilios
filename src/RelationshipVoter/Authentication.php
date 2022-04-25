<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\AuthenticationInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Authentication extends AbstractVoter
{
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof AuthenticationInterface
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
            self::VIEW => $user->performsNonLearnerFunction(),
            self::CREATE => $this->permissionChecker->canUpdateUser($user, $subject->getUser()->getSchool()->getId()),
            self::EDIT => $this->permissionChecker->canUpdateUser($user, $subject->getUser()->getSchool()->getId()),
            self::DELETE => $this->permissionChecker->canUpdateUser($user, $subject->getUser()->getSchool()->getId()),
            default => false,
        };
    }
}
