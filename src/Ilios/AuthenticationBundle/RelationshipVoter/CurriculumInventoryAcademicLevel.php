<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface;
use Ilios\CoreBundle\Entity\DTO\CurriculumInventoryAcademicLevelDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CurriculumInventoryAcademicLevel extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return (
            ($subject instanceof CurriculumInventoryAcademicLevelDTO && in_array($attribute, [self::VIEW])) or
            ($subject instanceof CurriculumInventoryAcademicLevelInterface && in_array($attribute, [self::VIEW]))
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

        if ($subject instanceof CurriculumInventoryAcademicLevelDTO) {
            return $this->voteOnDTO($user, $subject);
        }

        if ($subject instanceof CurriculumInventoryAcademicLevelInterface) {
            return $this->voteOnEntity($user, $subject);
        }

        return false;
    }

    protected function voteOnDTO(SessionUserInterface $sessionUser, CurriculumInventoryAcademicLevelDTO $level): bool
    {
        return $this->permissionChecker->canReadCurriculumInventoryReport(
            $sessionUser,
            $level->report,
            $level->school
        );
    }

    protected function voteOnEntity(
        SessionUserInterface $sessionUser,
        CurriculumInventoryAcademicLevelInterface $level
    ): bool {
        return $this->permissionChecker->canReadCurriculumInventoryReport(
            $sessionUser,
            $level->getReport()->getId(),
            $level->getReport()->getSchool()->getId()
        );
    }
}
