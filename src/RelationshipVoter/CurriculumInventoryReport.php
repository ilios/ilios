<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\CurriculumInventoryReportInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CurriculumInventoryReport extends AbstractVoter
{
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof CurriculumInventoryReportInterface
            && in_array($attribute, [self::CREATE, self::VIEW, self::EDIT, self::DELETE, self::ROLLOVER]);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
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
        return match ($attribute) {
            self::CREATE, self::ROLLOVER => $this->permissionChecker->canCreateCurriculumInventoryReport(
                $user,
                $subject->getSchool()->getId()
            ),
            self::EDIT => $this->permissionChecker->canUpdateCurriculumInventoryReport(
                $user,
                $subject->getId(),
                $subject->getSchool()->getId()
            ),
            self::DELETE => $this->permissionChecker->canDeleteCurriculumInventoryReport(
                $user,
                $subject->getId(),
                $subject->getSchool()->getId()
            ),
            default => false,
        };
    }
}
