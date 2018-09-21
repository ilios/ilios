<?php

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\AuthenticationInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Authentication extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof AuthenticationInterface
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
            case self::CREATE:
                return $this->permissionChecker->canUpdateUser($user, $subject->getUser()->getSchool()->getId());
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateUser($user, $subject->getUser()->getSchool()->getId());
                break;
            case self::DELETE:
                return $this->permissionChecker->canUpdateUser($user, $subject->getUser()->getSchool()->getId());
                break;
        }

        return false;
    }
}
