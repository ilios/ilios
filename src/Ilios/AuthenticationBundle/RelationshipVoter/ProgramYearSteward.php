<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\ProgramYearStewardInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ProgramYearSteward extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof ProgramYearStewardInterface
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
                return $this->permissionChecker->canUpdateProgramYear(
                    $user,
                    $subject->getProgramYear()->getId(),
                    $subject->getProgram()->getId(),
                    $subject->getProgramOwningSchool()->getId()
                );
                break;
        }

        return false;
    }
}
