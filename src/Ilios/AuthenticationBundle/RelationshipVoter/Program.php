<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\ProgramInterface;
use Ilios\CoreBundle\Entity\DTO\ProgramDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Program extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return (
            ($subject instanceof ProgramDTO && in_array($attribute, [self::VIEW])) or
            ($subject instanceof ProgramInterface && in_array($attribute, [
                    self::CREATE, self::VIEW, self::EDIT, self::DELETE
                ]))
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

        if ($subject instanceof ProgramDTO) {
            return $this->voteOnDTO($user, $subject);
        }

        if ($subject instanceof ProgramInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
    }

    protected function voteOnDTO(SessionUserInterface $sessionUser, ProgramDTO $program): bool
    {
        return $this->permissionChecker->canReadProgram($sessionUser, $program->id, $program->school);
    }

    protected function voteOnEntity(string $attribute, SessionUserInterface $sessionUser, ProgramInterface $program): bool
    {
        switch ($attribute) {
            case self::VIEW:
                return $this->permissionChecker->canReadProgram(
                    $sessionUser,
                    $program->getId(),
                    $program->getSchool()->getId()
                );
                break;
            case self::CREATE:
                return $this->permissionChecker->canCreateProgram($sessionUser, $program->getSchool()->getId());
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateProgram(
                    $sessionUser,
                    $program->getId(),
                    $program->getSchool()->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteProgram(
                    $sessionUser,
                    $program->getId(),
                    $program->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
