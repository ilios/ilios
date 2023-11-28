<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Classes\VoterPermissions;
use App\Entity\SessionLearningMaterialInterface;
use App\Service\SessionUserPermissionChecker;
use DateTime;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class SessionLearningMaterial extends AbstractVoter
{
    public function __construct(SessionUserPermissionChecker $permissionChecker)
    {
        parent::__construct(
            $permissionChecker,
            SessionLearningMaterialInterface::class,
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
        return match ($attribute) {
            VoterPermissions::VIEW =>
                $user->performsNonLearnerFunction() ||
                ($this->canLearnerSeeMaterial($subject) && $user->isLearnerInSession($subject->getSession()->getId())),
            VoterPermissions::EDIT,
            VoterPermissions::CREATE,
            VoterPermissions::DELETE => $this->permissionChecker->canUpdateSession(
                $user,
                $subject->getSession()
            ),
            default => false,
        };
    }

    protected function canLearnerSeeMaterial(SessionLearningMaterialInterface $material): bool
    {
        $now = new DateTime();
        $startDate = $material->getStartDate();
        $endDate = $material->getEndDate();
        if (isset($startDate) && isset($endDate)) {
            return $startDate < $now && $endDate > $now;
        } elseif (isset($startDate)) {
            return $startDate < $now;
        } elseif (isset($endDate)) {
            return $endDate > $now;
        }

        return true;
    }
}
