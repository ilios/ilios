<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class School extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof SchoolInterface
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

        if ($subject instanceof SchoolInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
    }

    protected function voteOnEntity(string $attribute, SessionUserInterface $sessionUser, SchoolInterface $school): bool
    {
        switch ($attribute) {
            case self::VIEW:
                return $this->permissionChecker->canReadSchool(
                    $sessionUser,
                    $school->getId()
                );
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateSchool(
                    $sessionUser,
                    $school->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteSchool(
                    $sessionUser,
                    $school->getId()
                );
                break;
        }

        return false;
    }
}
