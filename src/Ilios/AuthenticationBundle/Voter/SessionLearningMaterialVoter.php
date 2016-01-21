<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;
use Ilios\CoreBundle\Entity\SessionLearningMaterialInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class SessionVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class SessionLearningMaterialVoter extends SessionVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof SessionLearningMaterialInterface && in_array($attribute, array(
            self::CREATE, self::VIEW, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param SessionLearningMaterialInterface $material
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $material, TokenInterface $token)
    {
        $session = $material->getSession();
        if (! $session) {
            return false;
        }
        // grant perms based on the owning session
        $granted = parent::voteOnAttribute($attribute, $session, $token);

        // prevent access if associated LM is in draft, and the current user has no elevated privileges.
        if ($granted && self::VIEW === $attribute) {
            return $material->getLearningMaterial()->getStatus() !== LearningMaterialStatusInterface::IN_DRAFT
            || $this->userHasRole($token->getUser(), ['Faculty', 'Course Director', 'Developer']);
        }

        return $granted;
    }
}
