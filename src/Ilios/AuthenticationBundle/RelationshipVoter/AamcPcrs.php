<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\AamcPcrsInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AamcPcrs extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof AamcPcrsInterface
            && in_array(
                $attribute,
                [self::CREATE, self::VIEW, self::EDIT, self::DELETE]
            );
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

        if ($subject instanceof AamcPcrsInterface) {
            return $this->voteOnEntity($attribute);
        }

        return false;
    }

    protected function voteOnEntity(string $attribute): bool
    {
        if (self::VIEW === $attribute) {
            return true;
        }

        return false;
    }
}
