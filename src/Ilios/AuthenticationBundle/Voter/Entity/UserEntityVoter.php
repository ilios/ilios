<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class UserEntityVoter
 */
class UserEntityVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof UserInterface && in_array($attribute, array(
            self::CREATE, self::VIEW, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param UserInterface $requestedUser
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $requestedUser, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        if ($user->isRoot()) {
            return true;
        }

        switch ($attribute) {
            // at least one of these must be true.
            // 1. the requested user is the current user
            // 2. the current user has faculty/course director/developer role
            case self::VIEW:
                return (
                    $user->getId() === $requestedUser->getId()
                    || $user->hasRole(['Course Director', 'Faculty', 'Developer'])
                );
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                return $this->canCreateEditDeleteUser($user, $requestedUser);
                break;
        }
        return false;
    }

    /**
     * @param SessionUserInterface $user
     * @param UserInterface $requestedUser
     * @return bool
     */
    protected function canCreateEditDeleteUser(SessionUserInterface $user, UserInterface $requestedUser)
    {
        // only root users can edit/delete/create root users
        if (! $user->isRoot() && $requestedUser->isRoot()) {
            return false;
        }

        $schoolIds = $requestedUser->getAllSchools()->map(function (SchoolInterface $school) {
            return $school->getId();
        });

        // current user must have developer role and share the same school affiliations than the requested user.
        if ($user->hasRole(['Developer'])
            && ($user->isThePrimarySchool($requestedUser->getSchool())
                || $user->hasWritePermissionToSchools($schoolIds->toArray()))) {
            return true;
        }

        return false;
    }
}
