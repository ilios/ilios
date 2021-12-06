<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Entity\LearningMaterialInterface;
use App\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class LearningMaterial
 */
class LearningMaterial extends AbstractVoter
{
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof LearningMaterialInterface && in_array($attribute, [
                self::VIEW, self::CREATE, self::EDIT, self::DELETE
            ]);
    }

    /**
     * @param string $attribute
     * @param LearningMaterialInterface $learningMaterial
     * @param TokenInterface $token
     */
    protected function voteOnAttribute($attribute, $learningMaterial, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        if ($user->isRoot()) {
            return true;
        }

        switch ($attribute) {
            case self::VIEW:
                return true;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                return $user->performsNonLearnerFunction();
                break;
        }

        return false;
    }
}
