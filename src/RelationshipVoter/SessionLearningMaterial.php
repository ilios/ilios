<?php

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\SessionLearningMaterialInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class SessionLearningMaterial extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof SessionLearningMaterialInterface
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
            case self::EDIT:
            case self::CREATE:
            case self::DELETE:
                return $this->permissionChecker->canUpdateSession($user, $subject->getSession());
                break;
        }

        return false;
    }
}
