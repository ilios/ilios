<?php

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\CourseClerkshipTypeInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CourseClerkshipType extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CourseClerkshipTypeInterface
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

        if ($subject instanceof CourseClerkshipTypeInterface) {
            return self::VIEW === $attribute;
        }

        return false;
    }
}
