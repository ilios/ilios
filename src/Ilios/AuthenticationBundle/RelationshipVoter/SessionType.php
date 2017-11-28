<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\SessionTypeInterface;
use Ilios\CoreBundle\Entity\DTO\SessionTypeDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class SessionType extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return (
            ($subject instanceof SessionTypeDTO && in_array($attribute, [self::VIEW])) or
            ($subject instanceof SessionTypeInterface && in_array($attribute, [
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

        if ($subject instanceof SessionTypeDTO) {
            return $this->voteOnDTO($user, $subject);
        }

        if ($subject instanceof SessionTypeInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
    }

    protected function voteOnDTO(SessionUserInterface $sessionUser, SessionTypeDTO $sessionType): bool
    {
        return $this->permissionChecker->canReadSessionType($sessionUser, $sessionType->school);
    }

    protected function voteOnEntity(string $attribute, SessionUserInterface $sessionUser, SessionTypeInterface $sessionType): bool
    {
        switch ($attribute) {
            case self::VIEW:
                return $this->permissionChecker->canReadSessionType(
                    $sessionUser,
                    $sessionType->getSchool()->getId()
                );
                break;
            case self::CREATE:
                return $this->permissionChecker->canCreateSessionType($sessionUser, $sessionType->getSchool()->getId());
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateSessionType(
                    $sessionUser,
                    $sessionType->getSchool()->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteSessionType(
                    $sessionUser,
                    $sessionType->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
