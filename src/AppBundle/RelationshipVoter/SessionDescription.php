<?php

namespace AppBundle\RelationshipVoter;

use AppBundle\Classes\SessionUserInterface;
use AppBundle\Entity\SessionDescriptionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class SessionDescription extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof SessionDescriptionInterface
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
                break;
            case self::EDIT:
            case self::CREATE:
            case self::DELETE:
                return $this->permissionChecker->canUpdateSession($user, $subject->getSession());
                break;
        }

        return false;
    }
}
