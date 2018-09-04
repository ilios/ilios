<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use AppBundle\Entity\DTO\AuthenticationDTO;
use AppBundle\Entity\DTO\IngestionExceptionDTO;
use AppBundle\Entity\DTO\LearnerGroupDTO;
use AppBundle\Entity\DTO\OfferingDTO;
use AppBundle\Entity\DTO\PendingUserUpdateDTO;
use AppBundle\Entity\DTO\UserDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * @package Ilios\AuthenticationBundle\RelationshipVoter
 */
class UserDTOVoter extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::VIEW]) && $subject instanceof UserDTO;
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

        return $subject->id === $user->getId() || $user->performsNonLearnerFunction();
    }
}
