<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\CurriculumInventoryInstitutionInterface;
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

        if ($subject instanceof CurriculumInventoryInstitutionInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
    }

    protected function voteOnEntity(
        string $attribute,
        SessionUserInterface $sessionUser,
        CurriculumInventoryInstitutionInterface $institution
    ): bool {
        switch ($attribute) {
            case self::VIEW:
                return $this->permissionChecker->canReadCurriculumInventoryInstitution(
                    $sessionUser,
                    $institution->getSchool()->getId()
                );
                break;
            case self::CREATE:
                return $this->permissionChecker->canCreateCurriculumInventoryInstitution(
                    $sessionUser,
                    $institution->getSchool()->getId()
                );
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateCurriculumInventoryInstitution(
                    $sessionUser,
                    $institution->getSchool()->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteCurriculumInventoryInstitution(
                    $sessionUser,
                    $institution->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
