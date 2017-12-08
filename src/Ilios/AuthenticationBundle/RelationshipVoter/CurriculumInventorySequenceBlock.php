<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;
use Ilios\CoreBundle\Entity\DTO\CurriculumInventorySequenceBlockDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CurriculumInventorySequenceBlock extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return (
            ($subject instanceof CurriculumInventorySequenceBlockDTO && in_array($attribute, [self::VIEW])) or
            ($subject instanceof CurriculumInventorySequenceBlockInterface && in_array($attribute, [
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

        if ($subject instanceof CurriculumInventorySequenceBlockDTO) {
            return $this->voteOnDTO($user, $subject);
        }

        if ($subject instanceof CurriculumInventorySequenceBlockInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
    }

    protected function voteOnDTO(SessionUserInterface $sessionUser, CurriculumInventorySequenceBlockDTO $block): bool
    {
        return $this->permissionChecker->canReadCurriculumInventoryReport(
            $sessionUser,
            $block->report,
            $block->school
        );
    }

    protected function voteOnEntity(
        string $attribute,
        SessionUserInterface $sessionUser,
        CurriculumInventorySequenceBlockInterface $block
    ): bool {
        switch ($attribute) {
            case self::VIEW:
                return $this->permissionChecker->canReadCurriculumInventoryReport(
                    $sessionUser,
                    $block->getReport()->getId(),
                    $block->getReport()->getSchool()->getId()
                );
                break;
            case self::CREATE:
                return $this->permissionChecker->canUpdateCurriculumInventoryReport(
                    $sessionUser,
                    $block->getReport()->getId(),
                    $block->getReport()->getSchool()->getId()
                );
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateCurriculumInventoryReport(
                    $sessionUser,
                    $block->getReport()->getId(),
                    $block->getReport()->getSchool()->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canUpdateCurriculumInventoryReport(
                    $sessionUser,
                    $block->getReport()->getId(),
                    $block->getReport()->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
