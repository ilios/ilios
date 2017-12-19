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

        if ($subject instanceof ProgramYearInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
    }

    protected function voteOnEntity(
        string $attribute,
        SessionUserInterface $programYearUser,
        ProgramYearInterface $programYear
    ) : bool {
        switch ($attribute) {
            case self::VIEW:
                return $this->permissionChecker->canReadProgramYear(
                    $programYearUser,
                    $programYear->getId(),
                    $programYear->getProgram()->getId(),
                    $programYear->getSchool()->getId()
                );
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateProgramYear(
                    $programYearUser,
                    $programYear->getId(),
                    $programYear->getProgram()->getId(),
                    $programYear->getSchool()->getId()
                );
                break;
            case self::CREATE:
                return $this->permissionChecker->canCreateProgramYear(
                    $programYearUser,
                    $programYear->getProgram()->getId(),
                    $programYear->getSchool()->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteProgramYear(
                    $programYearUser,
                    $programYear->getId(),
                    $programYear->getProgram()->getId(),
                    $programYear->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
