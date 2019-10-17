<?php

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\SessionTypeInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class SessionType extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof SessionTypeInterface
            && in_array($attribute, [self::CREATE, self::VIEW, self::EDIT, self::DELETE]);
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
            case self::CREATE:
                return $this->permissionChecker->canCreateSessionType($user, $subject->getSchool()->getId());
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateSessionType(
                    $user,
                    $subject->getSchool()->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteSessionType(
                    $user,
                    $subject->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
