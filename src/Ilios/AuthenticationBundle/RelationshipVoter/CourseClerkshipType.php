<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\CourseClerkshipTypeInterface;
use Ilios\CoreBundle\Entity\DTO\CourseClerkshipTypeDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CourseClerkshipType extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return (
            ($subject instanceof CourseClerkshipTypeDTO && in_array($attribute, [self::VIEW])) or
            ($subject instanceof CourseClerkshipTypeInterface && in_array($attribute, [
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

        if ($subject instanceof CourseClerkshipTypeDTO) {
            return true;
        }

        if ($subject instanceof CourseClerkshipTypeInterface) {
            return self::VIEW === $attribute;
        }

        return false;
    }
}
