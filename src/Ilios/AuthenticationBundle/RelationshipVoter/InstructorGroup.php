<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\InstructorGroupInterface;
use Ilios\CoreBundle\Entity\DTO\InstructorGroupDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class InstructorGroup extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return (
            ($subject instanceof InstructorGroupDTO && in_array($attribute, [self::VIEW])) or
            ($subject instanceof InstructorGroupInterface && in_array($attribute, [
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

        if ($subject instanceof InstructorGroupDTO) {
            return $this->voteOnDTO($user, $subject);
        }

        if ($subject instanceof InstructorGroupInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
    }

    protected function voteOnDTO(SessionUserInterface $sessionUser, InstructorGroupDTO $vocabulary): bool
    {
        return $this->permissionChecker->canReadInstructorGroup($sessionUser, $vocabulary->school);
    }

    protected function voteOnEntity(
        string $attribute,
        SessionUserInterface $sessionUser,
        InstructorGroupInterface $vocabulary
    ): bool {
        switch ($attribute) {
            case self::VIEW:
                return $this->permissionChecker->canReadInstructorGroup(
                    $sessionUser,
                    $vocabulary->getSchool()->getId()
                );
                break;
            case self::CREATE:
                return $this->permissionChecker->canCreateInstructorGroup($sessionUser, $vocabulary->getSchool()->getId());
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateInstructorGroup(
                    $sessionUser,
                    $vocabulary->getSchool()->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteInstructorGroup(
                    $sessionUser,
                    $vocabulary->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
