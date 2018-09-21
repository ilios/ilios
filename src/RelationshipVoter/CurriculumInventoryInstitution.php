<?php

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\CurriculumInventoryInstitutionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CurriculumInventoryInstitution extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CurriculumInventoryInstitutionInterface
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
                return $this->permissionChecker->canCreateCurriculumInventoryInstitution(
                    $user,
                    $subject->getSchool()->getId()
                );
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateCurriculumInventoryInstitution(
                    $user,
                    $subject->getSchool()->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteCurriculumInventoryInstitution(
                    $user,
                    $subject->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
