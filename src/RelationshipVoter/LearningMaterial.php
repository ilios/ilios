<?php

namespace App\RelationshipVoter;

use App\Entity\LearningMaterialInterface;
use App\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class LearningMaterial
 */
class LearningMaterial extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof LearningMaterialInterface && in_array($attribute, array(
                self::VIEW, self::CREATE, self::EDIT, self::DELETE
            ));
    }

    /**
     * @param string $attribute
     * @param LearningMaterialInterface $learningMaterial
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $learningMaterial, TokenInterface $token)
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
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                return $user->performsNonLearnerFunction();
                break;
        }

        return false;
    }
}
