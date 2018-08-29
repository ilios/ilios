<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use AppBundle\Entity\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Session extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof SessionInterface
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
                return $user->performsNonLearnerFunction();
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateSession($user, $subject);
                break;
            case self::CREATE:
                return $this->permissionChecker->canCreateSession($user, $subject->getCourse());
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteSession($user, $subject);
                break;
        }

        return false;
    }
}
