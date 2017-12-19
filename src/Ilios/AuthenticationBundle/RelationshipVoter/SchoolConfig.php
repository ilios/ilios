<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\SchoolConfigInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class SchoolConfig extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof SchoolConfig
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

        if ($subject instanceof SchoolConfigInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
    }

    protected function voteOnEntity(
        string $attribute,
        SessionUserInterface $sessionUser,
        SchoolConfigInterface $schoolConfig
    ): bool {
        switch ($attribute) {
            case self::VIEW:
                return $this->permissionChecker->canReadSchoolConfig(
                    $sessionUser,
                    $schoolConfig->getSchool()->getId()
                );
                break;
            case self::CREATE:
                return $this->permissionChecker->canCreateSchoolConfig(
                    $sessionUser,
                    $schoolConfig->getSchool()->getId()
                );
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateSchoolConfig(
                    $sessionUser,
                    $schoolConfig->getSchool()->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteSchoolConfig(
                    $sessionUser,
                    $schoolConfig->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
