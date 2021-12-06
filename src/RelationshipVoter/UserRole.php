<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\AamcMethodInterface;
use App\Entity\UserRoleInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserRole extends AbstractVoter
{
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof UserRoleInterface
            && self::VIEW === $attribute;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        return true;
    }
}
