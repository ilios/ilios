<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\UserMaterial as Material;
use App\Classes\SessionUserInterface;
use App\Entity\LearningMaterialStatusInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class UserMaterial
 */
class UserMaterial extends AbstractVoter
{
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof Material && $attribute === self::VIEW;
    }

    /**
     * @param string $attribute
     * @param Material $material
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $material, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        // root user can see all user events
        if ($user->isRoot()) {
            return true;
        }

        // Deny access to LMs that are 'in draft' if the current user
        // does not perform a non-learner function.
        return LearningMaterialStatusInterface::IN_DRAFT !== $material->status
            || $user->performsNonLearnerFunction();
    }
}
