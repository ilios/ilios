<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\DTO\AuthenticationDTO;
use Ilios\CoreBundle\Entity\DTO\IngestionExceptionDTO;
use Ilios\CoreBundle\Entity\DTO\LearnerGroupDTO;
use Ilios\CoreBundle\Entity\DTO\OfferingDTO;
use Ilios\CoreBundle\Entity\DTO\PendingUserUpdateDTO;
use Ilios\CoreBundle\Entity\DTO\UserDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Grants VIEW permissions on all supported DTOs if the given user
 * fulfills a function that requires elevated permissions, such as
 * administrating courses, teaching sessions, directing programs, etc.
 *
 * @package Ilios\AuthenticationBundle\RelationshipVoter
 */
class ElevatedPermissionsViewDTOVoter extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        if ($this->abstain) {
            return false;
        }

        return (
            array($attribute, [self::VIEW]) && (
                $subject instanceof AuthenticationDTO
                || $subject instanceof IngestionExceptionDTO
                || $subject instanceof LearnerGroupDTO
                || $subject instanceof OfferingDTO
                || $subject instanceof PendingUserUpdateDTO
                || $subject instanceof UserDTO

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
