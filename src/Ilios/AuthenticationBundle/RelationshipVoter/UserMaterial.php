<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use AppBundle\Classes\UserMaterial as Material;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use AppBundle\Entity\LearningMaterialStatusInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class UserMaterial
 */
class UserMaterial extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof Material && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param Material $material
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $material, TokenInterface $token)
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
