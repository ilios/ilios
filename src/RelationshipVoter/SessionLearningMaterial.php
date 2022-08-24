<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\SessionLearningMaterialInterface;
use DateTime;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class SessionLearningMaterial extends AbstractVoter
{
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof SessionLearningMaterialInterface
            && in_array($attribute, [self::CREATE, self::VIEW, self::EDIT, self::DELETE]);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }
        if ($user->isRoot()) {
            return true;
        }
        return match ($attribute) {
            self::VIEW =>
                $user->performsNonLearnerFunction() ||
                ($this->canLearnerSeeMaterial($subject) && $user->isLearnerInSession($subject->getSession()->getId())),
            self::EDIT, self::CREATE, self::DELETE => $this->permissionChecker->canUpdateSession(
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
