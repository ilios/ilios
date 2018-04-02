<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\LearningMaterialUserRoleInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class LearningMaterialUserRole extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof LearningMaterialUserRoleInterface
            && self::VIEW === $attribute;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }
        if ($user->isRoot()) {
            return true;
        }

        if ($subject instanceof LearningMaterialUserRoleInterface) {
            return self::VIEW === $attribute;
        }

        return false;
    }
}
