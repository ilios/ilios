<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\DTO\SessionLearningMaterialDTO;
use DateTime;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class SessionLearningMaterialDTOVoter extends AbstractVoter
{
    protected function supports($attribute, $subject): bool
    {
        return $attribute === self::VIEW && $subject instanceof SessionLearningMaterialDTO;
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

        if ($user->performsNonLearnerFunction()) {
            return true;
        }

        return $this->canLearnerSeeMaterial($subject) && $user->isLearnerInSession($subject->session);
    }

    protected function canLearnerSeeMaterial(SessionLearningMaterialDTO $material): bool
    {
        $now = new DateTime();
        if (isset($material->startDate) && isset($material->endDate)) {
            return $material->startDate < $now && $material->endDate > $now;
        } elseif (isset($material->startDate)) {
            return $material->startDate < $now;
        } elseif (isset($material->endDate)) {
            return $material->endDate > $now;
        }

        return true;
    }
}
