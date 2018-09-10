<?php

namespace AppBundle\RelationshipVoter;

use AppBundle\Classes\SessionUserInterface;
use AppBundle\Entity\CurriculumInventoryReportInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CurriculumInventoryReport extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CurriculumInventoryReportInterface
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

        if (self::VIEW === $attribute) {
            return true;
        }

        if ($subject->getExport()) {
            return false;
        }

        if ($user->isRoot()) {
            return true;
        }

        switch ($attribute) {
            case self::CREATE:
                return $this->permissionChecker->canCreateCurriculumInventoryReport(
                    $user,
                    $subject->getSchool()->getId()
                );
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateCurriculumInventoryReport(
                    $user,
                    $subject->getId(),
                    $subject->getSchool()->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteCurriculumInventoryReport(
                    $user,
                    $subject->getId(),
                    $subject->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
