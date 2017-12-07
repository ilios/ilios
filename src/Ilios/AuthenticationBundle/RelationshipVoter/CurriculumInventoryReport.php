<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;
use Ilios\CoreBundle\Entity\DTO\CurriculumInventoryReportDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CurriculumInventoryReport extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return (
            ($subject instanceof CurriculumInventoryReportDTO && in_array($attribute, [self::VIEW])) or
            ($subject instanceof CurriculumInventoryReportInterface && in_array($attribute, [
                    self::CREATE, self::VIEW, self::EDIT, self::DELETE
                ]))
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

        if ($subject instanceof CurriculumInventoryReportDTO) {
            return $this->voteOnDTO($user, $subject);
        }

        if ($subject instanceof CurriculumInventoryReportInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
    }

    protected function voteOnDTO(SessionUserInterface $sessionUser, CurriculumInventoryReportDTO $course): bool
    {
        return $this->permissionChecker->canReadCurriculumInventoryReport($sessionUser, $course->id, $course->school);
    }

    protected function voteOnEntity(string $attribute, SessionUserInterface $sessionUser, CurriculumInventoryReportInterface $course): bool
    {
        switch ($attribute) {
            case self::VIEW:
                return $this->permissionChecker->canReadCurriculumInventoryReport(
                    $sessionUser,
                    $course->getId(),
                    $course->getSchool()->getId()
                );
                break;
            case self::CREATE:
                return $this->permissionChecker->canCreateCurriculumInventoryReport($sessionUser, $course->getSchool()->getId());
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateCurriculumInventoryReport(
                    $sessionUser,
                    $course->getId(),
                    $course->getSchool()->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteCurriculumInventoryReport(
                    $sessionUser,
                    $course->getId(),
                    $course->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
