<?php

namespace AppBundle\RelationshipVoter;

use AppBundle\Classes\SessionUserInterface;
use AppBundle\Entity\InstructorGroupInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class InstructorGroup extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof InstructorGroupInterface
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

        switch ($attribute) {
            case self::VIEW:
                return true;
                break;
            case self::CREATE:
                return $this->permissionChecker->canCreateInstructorGroup(
                    $user,
                    $subject->getSchool()->getId()
                );
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateInstructorGroup(
                    $user,
                    $subject->getSchool()->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteInstructorGroup(
                    $user,
                    $subject->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
