<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Classes\VoterPermissions;
use App\Entity\CurriculumInventoryReportInterface;
use App\Service\SessionUserPermissionChecker;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CurriculumInventoryReport extends AbstractVoter
{
    public function __construct(SessionUserPermissionChecker $permissionChecker)
    {
        parent::__construct(
            $permissionChecker,
            CurriculumInventoryReportInterface::class,
            [
                VoterPermissions::CREATE,
                VoterPermissions::VIEW,
                VoterPermissions::EDIT,
                VoterPermissions::DELETE,
                VoterPermissions::ROLLOVER,
            ]
        );
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        if (VoterPermissions::VIEW === $attribute) {
            return true;
        }

        if (VoterPermissions::ROLLOVER !== $attribute) {
            if ($subject->getExport()) {
                return false;
            }
        }

        if ($user->isRoot()) {
            return true;
        }
        return match ($attribute) {
            VoterPermissions::CREATE,
            VoterPermissions::ROLLOVER => $this->permissionChecker->canCreateCurriculumInventoryReport(
                $user,
                $subject->getSchool()->getId()
            ),
            VoterPermissions::EDIT => $this->permissionChecker->canUpdateCurriculumInventoryReport(
                $user,
                $subject->getId(),
                $subject->getSchool()->getId()
            ),
            VoterPermissions::DELETE => $this->permissionChecker->canDeleteCurriculumInventoryReport(
                $user,
                $subject->getId(),
                $subject->getSchool()->getId()
            ),
            default => false,
        };
    }
}
