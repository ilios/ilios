<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Classes\UserMaterial;
use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class UsermaterialVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class UsermaterialVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof UserMaterial && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param UserMaterial $material
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $material, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // Deny access to LMs that are 'in draft' if the current user
                // does not have elevated privileges.
                return LearningMaterialStatusInterface::IN_DRAFT !== $material->status
                    || $user->hasRole(['Faculty', 'Course Director', 'Developer']);
        }
        return false;
    }
}
