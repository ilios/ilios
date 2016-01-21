<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\LearningMaterialInterface;
use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class LearningMaterialVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class LearningMaterialVoter extends AbstractVoter
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
     * @param LearningMaterialInterface $material
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $material, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // Deny access to LMs that are 'in draft' if the current user
                // does not have elevated privileges.
                return LearningMaterialStatusInterface::IN_DRAFT !== $material->getLearningMaterial()->getStatus()->getId()
                    || $this->userHasRole($user, ['Faculty', 'Course Director', 'Developer']);
                break;
            case self::CREATE:
                // users with 'Faculty', 'Course director' or 'Developer' role can create materials.
                return $this->userHasRole($user, ['Faculty', 'Course Director', 'Developer']);
                break;
            case self::EDIT:
            case self::DELETE:
                // in order to grant EDIT and DELETE privileges on the given learning material to the given user,
                // at least one of the following statements must be true:
                // 1. the user owns the learning material
                // 2. the user has at least one of 'Faculty', 'Course Director' or 'Developer' roles.
                return (
                    $this->usersAreIdentical($user, $material->getOwningUser())
                    || $this->userHasRole($user, ['Faculty', 'Course Director', 'Developer'])
                );
                break;
        }

        return false;
    }
}
