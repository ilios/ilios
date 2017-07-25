<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\CoreBundle\Entity\DTO\LearningMaterialDTO;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class LearningMaterialDTOVoter
 */
class LearningMaterialDTOVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof LearningMaterialDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param LearningMaterialDTO $learningMaterialDTO
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $learningMaterialDTO, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // Deny access to LMs that are 'in draft' if the current user
                // does not have elevated privileges.
                return LearningMaterialStatusInterface::IN_DRAFT !== $learningMaterialDTO->status
                    || $user->hasRole(['Faculty', 'Course Director', 'Developer']);
        }
        return false;
    }
}
