<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Classes\VoterPermissions;
use App\Entity\CurriculumInventoryExportInterface;
use App\Service\SessionUserPermissionChecker;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CurriculumInventoryExport extends AbstractVoter
{
    public function __construct(SessionUserPermissionChecker $permissionChecker)
    {
        parent::__construct(
            $permissionChecker,
            CurriculumInventoryExportInterface::class,
            [
                VoterPermissions::CREATE,
                VoterPermissions::VIEW,
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

        if ($subject->getReport()->getExport()) {
            return false;
        }

        if ($user->isRoot()) {
            return true;
        }

        return $this->permissionChecker->canUpdateCurriculumInventoryReport(
            $user,
            $subject->getReport()->getId(),
            $subject->getReport()->getSchool()->getId()
        );
    }
}
