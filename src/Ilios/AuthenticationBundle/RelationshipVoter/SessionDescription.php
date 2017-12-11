<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\SessionDescriptionInterface;
use Ilios\CoreBundle\Entity\DTO\SessionDescriptionDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class SessionDescription extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return (
            ($subject instanceof SessionDescriptionDTO && in_array($attribute, [self::VIEW])) or
            ($subject instanceof SessionDescriptionInterface && in_array(
                    $attribute,
                    [
                        self::CREATE,
                        self::VIEW,
                        self::EDIT,
                        self::DELETE,
                    ]
                ))
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

        if ($subject instanceof SessionDescriptionDTO) {
            return $this->voteOnDTO($user, $subject);
        }

        if ($subject instanceof SessionDescriptionInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
    }

    protected function voteOnDTO(SessionUserInterface $sessionUser, SessionDescriptionDTO $sessionDescription): bool
    {
        return $this->permissionChecker->canReadSession(
            $sessionUser,
            $sessionDescription->session,
            $sessionDescription->course,
            $sessionDescription->school
        );
    }

    protected function voteOnEntity(
        string $attribute,
        SessionUserInterface $sessionUser,
        SessionDescriptionInterface $sessionDescription
    ): bool {
        switch ($attribute) {
            case self::VIEW:
                return $this->permissionChecker->canReadSession(
                    $sessionUser,
                    $sessionDescription->getSession()->getId(),
                    $sessionDescription->getSession()->getCourse()->getId(),
                    $sessionDescription->getSession()->getCourse()->getSchool()->getId()
                );
                break;
            case self::EDIT:
            case self::CREATE:
            case self::DELETE:
                return $this->permissionChecker->canUpdateSession(
                    $sessionUser,
                    $sessionDescription->getSession()->getId(),
                    $sessionDescription->getSession()->getCourse()->getId(),
                    $sessionDescription->getSession()->getCourse()->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
