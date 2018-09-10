<?php

namespace AppBundle\RelationshipVoter;

use AppBundle\Classes\SessionUserInterface;
use AppBundle\Entity\IlmSessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class IlmSession extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof IlmSessionInterface
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

        switch ($attribute) {
            case self::VIEW:
                return true;
            case self::EDIT:
            case self::CREATE:
            case self::DELETE:
                return $this->permissionChecker->canUpdateSession($user, $subject->getSession());
                break;
        }

        return false;
    }
}
