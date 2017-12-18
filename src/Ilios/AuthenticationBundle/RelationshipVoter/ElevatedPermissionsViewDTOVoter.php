<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\DTO\LearnerGroupDTO;
use Ilios\CoreBundle\Entity\DTO\OfferingDTO;
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
        return (
            array($attribute, [self::VIEW]) && (
                $subject instanceof OfferingDTO
                || $subject instanceof LearnerGroupDTO
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
