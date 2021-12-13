<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\LearningMaterialUserRoleInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class LearningMaterialUserRole extends AbstractVoter
{
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof LearningMaterialUserRoleInterface
            && self::VIEW === $attribute;
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

        if ($subject instanceof LearningMaterialUserRoleInterface) {
            return self::VIEW === $attribute;
        }

        return false;
    }
}
