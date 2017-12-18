<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\AssessmentOptionInterface;
use Ilios\CoreBundle\Entity\DTO\AssessmentOptionDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AssessmentOption extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return (
            ($subject instanceof AssessmentOptionDTO && in_array($attribute, [self::VIEW])) or
            ($subject instanceof AssessmentOptionInterface && in_array($attribute, [
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

        if ($subject instanceof AssessmentOptionDTO) {
            return true;
        }

        if ($subject instanceof AssessmentOptionInterface) {
            return self::VIEW === $attribute;
        }

        return false;
    }
}
