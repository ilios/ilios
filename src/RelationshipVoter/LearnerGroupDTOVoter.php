<?php

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\DTO\AuthenticationDTO;
use App\Entity\DTO\IngestionExceptionDTO;
use App\Entity\DTO\LearnerGroupDTO;
use App\Entity\DTO\OfferingDTO;
use App\Entity\DTO\PendingUserUpdateDTO;
use App\Entity\DTO\UserDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * @package App\RelationshipVoter
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
