<?php

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\ApplicationConfigInterface;
use App\Entity\DTO\ApplicationConfigDTO;
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

        return false;
    }
}
