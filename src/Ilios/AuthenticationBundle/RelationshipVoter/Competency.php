<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\CompetencyInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Competency extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        if ($this->abstain) {
            return false;
        }

        return $subject instanceof CompetencyInterface
            && in_array(
                $attribute,
                [self::CREATE, self::VIEW, self::EDIT, self::DELETE]
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
            case self::CREATE:
                return $this->permissionChecker->canCreateCompetency(
                    $user,
                    $subject->getSchool()->getId()
                );
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateCompetency(
                    $user,
                    $subject->getSchool()->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteCompetency(
                    $user,
                    $subject->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
