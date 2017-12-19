<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\IlmSessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class IlmSession extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof IlmSessionInterface
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

        if ($subject instanceof IlmSessionInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
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
