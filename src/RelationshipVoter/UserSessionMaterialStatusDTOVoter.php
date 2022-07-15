<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\DTO\UserSessionMaterialStatusDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * @package App\RelationshipVoter
 */
class UserSessionMaterialStatusDTOVoter extends AbstractVoter
{
    protected function supports($attribute, $subject): bool
    {
        return $attribute === self::VIEW && $subject instanceof UserSessionMaterialStatusDTO;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        return $subject->user === $user->getId();
    }
}
