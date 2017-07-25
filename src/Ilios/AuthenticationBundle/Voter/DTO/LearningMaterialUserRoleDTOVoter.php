<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\CoreBundle\Entity\DTO\LearningMaterialUserRoleDTO;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class LearningMaterialUserRoleDTOVoter
 */
class LearningMaterialUserRoleDTOVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof LearningMaterialUserRoleDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param LearningMaterialUserRoleDTO $learningMaterialUserRoleDto
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $learningMaterialUserRoleDto, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return true;
                break;
        }
        return false;
    }
}
