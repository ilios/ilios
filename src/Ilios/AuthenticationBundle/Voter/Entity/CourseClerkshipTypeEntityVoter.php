<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\CourseClerkshipTypeInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CourseClerkshipTypeEntityVoter
 */
class CourseClerkshipTypeEntityVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CourseClerkshipTypeInterface && in_array($attribute, [
            self::CREATE, self::VIEW, self::EDIT, self::DELETE
        ]);
    }

    /**
     * @param string $attribute
     * @param CourseClerkshipTypeInterface $type
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $type, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        // all authenticated users can view clerkship types,
        // but only developers can create/modify/delete them directly.
        switch ($attribute) {
            case self::VIEW:
                return true;
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                return $user->hasRole(['Developer']);
                break;
        }

        return false;
    }
}
