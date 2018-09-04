<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use AppBundle\Entity\LearningMaterialInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
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
                return true;
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                return $user->performsNonLearnerFunction();
                break;
        }

        return false;
    }
}
