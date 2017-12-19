<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\ProgramYearInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ProgramYear extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof ProgramYearInterface
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
                return $this->permissionChecker->canUpdateProgramYear(
                    $user,
                    $subject->getId(),
                    $subject->getProgram()->getId(),
                    $subject->getSchool()->getId()
                );
                break;
            case self::CREATE:
                return $this->permissionChecker->canCreateProgramYear(
                    $user,
                    $subject->getProgram()->getId(),
                    $subject->getSchool()->getId()
                );
                break;
            case self::DELETE:
            return $this->permissionChecker->canDeleteProgramYear(
                $user,
                $subject->getId(),
                $subject->getProgram()->getId(),
                $subject->getSchool()->getId()
            );
            break;
        }

        return false;
    }
}
