<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\LearnerGroupInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class LearnerGroup extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        if ($this->abstain) {
            return false;
        }

        return $subject instanceof LearnerGroupInterface
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
                return $this->permissionChecker->canCreateLearnerGroup($user, $subject->getSchool()->getId());
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateLearnerGroup(
                    $user,
                    $subject->getSchool()->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteLearnerGroup(
                    $user,
                    $subject->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
