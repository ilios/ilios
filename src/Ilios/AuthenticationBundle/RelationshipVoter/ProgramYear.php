<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\ProgramYearInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ProgramYear extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        if ($this->abstain) {
            return false;
        }

        return $subject instanceof ProgramYearInterface
            && in_array(
                $attribute,
                [self::CREATE, self::VIEW, self::EDIT, self::DELETE, self::UNLOCK, self::UNARCHIVE]
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
                return $this->permissionChecker->canUpdateProgramYear($user, $subject);
                break;
            case self::CREATE:
                return $this->permissionChecker->canCreateProgramYear($user, $subject->getProgram());
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteProgramYear($user, $subject);
                break;
            case self::UNLOCK:
                return $this->permissionChecker->canUnlockProgramYear($user, $subject);
                break;
            case self::UNARCHIVE:
                return $this->permissionChecker->canUnarchiveProgramYear($user, $subject);
                break;
        }

        return false;
    }
}
