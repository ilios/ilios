<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\UserInterface;
use App\Entity\UserSessionMaterialStatusInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserSessionMaterialStatus extends AbstractVoter
{
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof UserSessionMaterialStatusInterface
            && in_array($attribute, [self::CREATE, self::VIEW, self::EDIT, self::DELETE]);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }
        return match ($attribute) {
            self::VIEW, self::CREATE, self::EDIT, self::DELETE => $user->isTheUser($subject->getUser()),
            default => false,
        };
    }
}
