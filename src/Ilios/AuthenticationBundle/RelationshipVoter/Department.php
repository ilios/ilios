<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\DepartmentInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Department extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof DepartmentInterface
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

        if ($subject instanceof DepartmentInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
    }

    protected function voteOnEntity(
        string $attribute,
        SessionUserInterface $sessionUser,
        DepartmentInterface $department
    ): bool {
        switch ($attribute) {
            case self::VIEW:
                return $this->permissionChecker->canReadDepartment(
                    $sessionUser,
                    $department->getSchool()->getId()
                );
                break;
            case self::CREATE:
                return $this->permissionChecker->canCreateDepartment($sessionUser, $department->getSchool()->getId());
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateDepartment(
                    $sessionUser,
                    $department->getSchool()->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteDepartment(
                    $sessionUser,
                    $department->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
