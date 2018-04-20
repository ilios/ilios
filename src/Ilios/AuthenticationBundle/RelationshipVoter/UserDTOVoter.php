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
