<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use AppBundle\Entity\AamcMethodInterface;
use AppBundle\Entity\UserRoleInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserRole extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof UserRoleInterface
            && self::VIEW === $attribute;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        return true;
    }
}
