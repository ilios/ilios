<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;
use Ilios\CoreBundle\Entity\DTO\LearningMaterialStatusDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class LearningMaterialStatus extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return (
            ($subject instanceof LearningMaterialStatusDTO && in_array($attribute, [self::VIEW])) or
            ($subject instanceof LearningMaterialStatusInterface && in_array($attribute, [
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

        if ($subject instanceof LearningMaterialStatusDTO) {
            return true;
        }

        if ($subject instanceof LearningMaterialStatusInterface) {
            return self::VIEW === $attribute;
        }

        return false;
    }
}
