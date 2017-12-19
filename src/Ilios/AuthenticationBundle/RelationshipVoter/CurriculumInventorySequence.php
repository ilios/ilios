<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CurriculumInventorySequence extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CurriculumInventorySequenceInterface
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

        if ($subject instanceof CurriculumInventorySequenceInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
    }

    protected function voteOnEntity(
        string $attribute,
        SessionUserInterface $sessionUser,
        CurriculumInventorySequenceInterface $sequence
    ): bool {
        switch ($attribute) {
            case self::VIEW:
                return $this->permissionChecker->canReadCurriculumInventoryReport(
                    $sessionUser,
                    $sequence->getReport()->getId(),
                    $sequence->getReport()->getSchool()->getId()
                );
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                return $this->permissionChecker->canUpdateCurriculumInventoryReport(
                    $sessionUser,
                    $sequence->getReport()->getId(),
                    $sequence->getReport()->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
