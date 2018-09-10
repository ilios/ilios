<?php

namespace AppBundle\RelationshipVoter;

use AppBundle\Classes\SessionUserInterface;
use AppBundle\Entity\CurriculumInventoryAcademicLevelInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CurriculumInventoryAcademicLevel extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CurriculumInventoryAcademicLevelInterface && in_array($attribute, [self::VIEW]);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        return true;
    }
}
