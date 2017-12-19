<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\CohortInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Cohort extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CohortInterface
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

        if ($subject instanceof CohortInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
    }

    protected function voteOnEntity(
        string $attribute,
        SessionUserInterface $cohortUser,
        CohortInterface $cohort
    ) : bool {
        switch ($attribute) {
            case self::VIEW:
                return $this->permissionChecker->canReadCohort(
                    $cohortUser,
                    $cohort->getId(),
                    $cohort->getProgram()->getId(),
                    $cohort->getSchool()->getId()
                );
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateCohort(
                    $cohortUser,
                    $cohort->getId(),
                    $cohort->getProgram()->getId(),
                    $cohort->getSchool()->getId()
                );
                break;
            case self::CREATE:
                return $this->permissionChecker->canCreateCohort(
                    $cohortUser,
                    $cohort->getProgram()->getId(),
                    $cohort->getSchool()->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteCohort(
                    $cohortUser,
                    $cohort->getId(),
                    $cohort->getProgram()->getId(),
                    $cohort->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
