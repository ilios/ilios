<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\IlmSessionInterface;
use Ilios\CoreBundle\Entity\DTO\IlmSessionDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class IlmSession extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return (
            ($subject instanceof IlmSessionDTO && in_array($attribute, [self::VIEW])) or
            ($subject instanceof IlmSessionInterface && in_array(
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

        if ($subject instanceof IlmSessionDTO) {
            return $this->voteOnDTO($user, $subject);
        }

        if ($subject instanceof IlmSessionInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
    }

    protected function voteOnDTO(SessionUserInterface $sessionUser, IlmSessionDTO $ilmSession): bool
    {
        return $this->permissionChecker->canReadSession(
            $sessionUser,
            $ilmSession->session,
            $ilmSession->course,
            $ilmSession->school
        );
    }

    protected function voteOnEntity(
        string $attribute,
        SessionUserInterface $sessionUser,
        IlmSessionInterface $ilmSession
    ): bool {
        switch ($attribute) {
            case self::VIEW:
                return $this->permissionChecker->canReadSession(
                    $sessionUser,
                    $ilmSession->getSession()->getId(),
                    $ilmSession->getSession()->getCourse()->getId(),
                    $ilmSession->getSession()->getCourse()->getSchool()->getId()
                );
                break;
            case self::EDIT:
            case self::CREATE:
            case self::DELETE:
                return $this->permissionChecker->canUpdateSession(
                    $sessionUser,
                    $ilmSession->getSession()->getId(),
                    $ilmSession->getSession()->getCourse()->getId(),
                    $ilmSession->getSession()->getCourse()->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
