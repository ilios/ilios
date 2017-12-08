<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\CurriculumInventoryExportInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CurriculumInventoryExport extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return (
            $subject instanceof CurriculumInventoryExportInterface
            && in_array($attribute, [self::CREATE, self::VIEW])
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

        if ($subject instanceof CurriculumInventoryExportInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
    }

    protected function voteOnEntity(
        string $attribute,
        SessionUserInterface $sessionUser,
        CurriculumInventoryExportInterface $sequence
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
