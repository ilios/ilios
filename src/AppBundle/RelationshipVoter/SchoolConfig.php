<?php

namespace AppBundle\RelationshipVoter;

use AppBundle\Classes\SessionUserInterface;
use AppBundle\Entity\SchoolConfigInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class SchoolConfig extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof SchoolConfigInterface
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
            case self::EDIT:
            case self::DELETE:
                return $this->permissionChecker->canUpdateSchoolConfig(
                    $user,
                    $subject->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
