<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\DTO\AuthenticationDTO;
use App\Entity\DTO\CourseLearningMaterialDTO;
use App\Entity\DTO\IngestionExceptionDTO;
use App\Entity\DTO\OfferingDTO;
use App\Entity\DTO\PendingUserUpdateDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Grants VIEW permissions on all supported DTOs if the given user
 * fulfills a function that requires elevated permissions, such as
 * administrating courses, teaching sessions, directing programs, etc.
 *
 * @package App\RelationshipVoter
 */
class ElevatedPermissionsViewDTOVoter extends AbstractVoter
{
    protected function supports($attribute, $subject): bool
    {
        return (
            $attribute === self::VIEW && (
                $subject instanceof AuthenticationDTO
                || $subject instanceof CourseLearningMaterialDTO
                || $subject instanceof IngestionExceptionDTO
                || $subject instanceof OfferingDTO
                || $subject instanceof PendingUserUpdateDTO
            )
        );
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

        return $user->performsNonLearnerFunction();
    }
}
