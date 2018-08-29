<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use AppBundle\Entity\CohortInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Cohort extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CohortInterface
            && in_array(
                $attribute,
                [self::VIEW, self::EDIT]
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

        switch ($attribute) {
            case self::VIEW:
                return true;
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateProgramYear($user, $subject->getProgramYear());
                break;
        }

        return false;
    }
}
