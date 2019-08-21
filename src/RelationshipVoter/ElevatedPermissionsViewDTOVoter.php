<?php

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\DTO\AuthenticationDTO;
use App\Entity\DTO\CourseLearningMaterialDTO;
use App\Entity\DTO\IngestionExceptionDTO;
use App\Entity\DTO\LearnerGroupDTO;
use App\Entity\DTO\LearningMaterialDTO;
use App\Entity\DTO\OfferingDTO;
use App\Entity\DTO\PendingUserUpdateDTO;
use App\Entity\DTO\SessionLearningMaterialDTO;
use App\Entity\DTO\UserDTO;
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
    protected function supports($attribute, $subject)
    {
        return (
            in_array($attribute, [self::VIEW]) && (
                $subject instanceof AuthenticationDTO
                || $subject instanceof CourseLearningMaterialDTO
                || $subject instanceof IngestionExceptionDTO
                || $subject instanceof OfferingDTO
                || $subject instanceof PendingUserUpdateDTO
                || $subject instanceof SessionLearningMaterialDTO
            )
        );
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
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
