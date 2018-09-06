<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use AppBundle\Classes\SessionUserInterface;
use AppBundle\Entity\ProgramYearInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ProgramYear extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof ProgramYearInterface
            && in_array(
                $attribute,
                [
                    self::CREATE,
                    self::VIEW,
                    self::EDIT,
                    self::DELETE,
                    self::UNLOCK,
                    self::LOCK,
                    self::ARCHIVE,
                ]
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
            case self::ARCHIVE:
                return $this->permissionChecker->canArchiveProgramYear($user, $subject);
                break;
            case self::LOCK:
                return $this->permissionChecker->canLockProgramYear($user, $subject);
                break;
        }

        return false;
    }
}
