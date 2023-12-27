<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Classes\VoterPermissions;
use App\Entity\ProgramYearInterface;
use App\Entity\ProgramYearObjectiveInterface;
use App\Service\SessionUserPermissionChecker;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class Objective
 */
class ProgramYearObjective extends AbstractVoter
{
    public function __construct(SessionUserPermissionChecker $permissionChecker)
    {
        parent::__construct(
            $permissionChecker,
            ProgramYearObjectiveInterface::class,
            [
                VoterPermissions::CREATE,
                VoterPermissions::VIEW,
                VoterPermissions::EDIT,
                VoterPermissions::DELETE,
            ]
        );
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        if ($user->isRoot()) {
            return true;
        }

        switch ($attribute) {
            case VoterPermissions::VIEW:
                return true;
            case VoterPermissions::CREATE:
            case VoterPermissions::EDIT:
            case VoterPermissions::DELETE:
                $programYear = $subject->getProgramYear();
                return $this->permissionChecker->canUpdateProgramYear($user, $programYear);
        }

        return false;
    }
}
