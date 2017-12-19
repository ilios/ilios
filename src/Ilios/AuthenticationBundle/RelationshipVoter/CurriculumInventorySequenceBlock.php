<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CurriculumInventorySequenceBlock extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CurriculumInventorySequenceBlockInterface
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

        if ($subject instanceof CurriculumInventorySequenceBlockInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
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
            case self::EDIT:
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
