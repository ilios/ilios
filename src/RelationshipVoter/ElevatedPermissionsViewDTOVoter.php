<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Classes\VoterPermissions;
use App\Entity\DTO\AuthenticationDTO;
use App\Entity\DTO\CourseLearningMaterialDTO;
use App\Entity\DTO\IngestionExceptionDTO;
use App\Entity\DTO\OfferingDTO;
use App\Entity\DTO\PendingUserUpdateDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Grants VIEW permissions on all supported DTOs if the given user
 * fulfills a function that requires elevated permissions, such as
 * administrating courses, teaching sessions, directing programs, etc.
 *
 * @package App\RelationshipVoter
 */
class ElevatedPermissionsViewDTOVoter extends Voter
{
    public function supportsAttribute(string $attribute): bool
    {
        return $attribute === VoterPermissions::VIEW;
    }

    public function supportsType(string $subjectType): bool
    {
        return (
            is_a($subjectType, AuthenticationDTO::class, true)
            || is_a($subjectType, CourseLearningMaterialDTO::class, true)
            || is_a($subjectType, IngestionExceptionDTO::class, true)
            || is_a($subjectType, OfferingDTO::class, true)
            || is_a($subjectType, PendingUserUpdateDTO::class, true)
        );
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return (
            $this->supportsAttribute($attribute) && (
                $subject instanceof AuthenticationDTO
                || $subject instanceof CourseLearningMaterialDTO
                || $subject instanceof IngestionExceptionDTO
                || $subject instanceof OfferingDTO
                || $subject instanceof PendingUserUpdateDTO
            )
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

        return $user->performsNonLearnerFunction();
    }
}
