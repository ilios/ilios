<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\ApplicationConfigInterface;
use Ilios\CoreBundle\Entity\DTO\ApplicationConfigDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ApplicationConfig extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return (
            ($subject instanceof ApplicationConfigDTO && in_array($attribute, [self::VIEW])) or
            ($subject instanceof ApplicationConfigInterface && in_array($attribute, [
                    self::CREATE, self::VIEW, self::EDIT, self::DELETE
                ]))
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

        if ($subject instanceof ApplicationConfigDTO) {
            return true;
        }

        if ($subject instanceof ApplicationConfigInterface) {
            return self::VIEW === $attribute;
        }

        return false;
    }
}
