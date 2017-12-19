<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Session extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof SessionInterface
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

        if ($subject instanceof SessionInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
    }

    protected function voteOnEntity(
        string $attribute,
        SessionUserInterface $sessionUser,
        SessionInterface $session
    ) : bool {
        switch ($attribute) {
            case self::VIEW:
                return $this->permissionChecker->canReadSession(
                    $sessionUser,
                    $session->getId(),
                    $session->getCourse()->getId(),
                    $session->getSchool()->getId()
                );
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateSession(
                    $sessionUser,
                    $session->getId(),
                    $session->getCourse()->getId(),
                    $session->getSchool()->getId()
                );
                break;
            case self::CREATE:
                return $this->permissionChecker->canCreateSession(
                    $sessionUser,
                    $session->getCourse()->getId(),
                    $session->getSchool()->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteSession(
                    $sessionUser,
                    $session->getId(),
                    $session->getCourse()->getId(),
                    $session->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
