<?php

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\CurriculumInventoryReportInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CurriculumInventoryReport extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CurriculumInventoryReportInterface
            && in_array($attribute, [self::CREATE, self::VIEW, self::EDIT, self::DELETE, self::ROLLOVER]);
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

        if (self::ROLLOVER !== $attribute) {
            if ($subject->getExport()) {
                return false;
            }
        }

        if ($user->isRoot()) {
            return true;
        }

        switch ($attribute) {
            case self::CREATE:
            case self::ROLLOVER:
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
