<?php

namespace AppBundle\RelationshipVoter;

use AppBundle\Classes\SessionUserInterface;
use AppBundle\Entity\DTO\AuthenticationDTO;
use AppBundle\Entity\DTO\IngestionExceptionDTO;
use AppBundle\Entity\DTO\LearnerGroupDTO;
use AppBundle\Entity\DTO\OfferingDTO;
use AppBundle\Entity\DTO\PendingUserUpdateDTO;
use AppBundle\Entity\DTO\UserDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * @package AppBundle\RelationshipVoter
 */
class LearnerGroupDTOVoter extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::VIEW]) && $subject instanceof LearnerGroupDTO;
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

        return $this->permissionChecker->canViewLearnerGroup($user, $subject->id);
    }
}
