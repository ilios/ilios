<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CurriculumInventorySequence extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        if ($this->abstain) {
            return false;
        }

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

        if (self::VIEW === $attribute) {
            return true;
        }

        if ($subject->getReport()->getExport()) {
            return false;
        }

        if ($user->isRoot()) {
            return true;
        }

        return $this->permissionChecker->canUpdateCurriculumInventoryReport(
            $user,
            $subject->getReport()->getId(),
            $subject->getReport()->getSchool()->getId()
        );
    }
}
