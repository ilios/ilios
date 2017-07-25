<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\CoreBundle\Entity\AuthenticationInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class AuthenticationVoter
 */
class AuthenticationEntityVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof AuthenticationInterface && in_array($attribute, array(
            self::VIEW, self::CREATE, self::EDIT, self::DELETE

        ));
    }

    /**
     * @param string $attribute
     * @param AuthenticationInterface $authentication
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $authentication, TokenInterface $token)
    {
        /** @var SessionUserInterface $user */
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            // at least one of these must be true.
            // 1. the requested authentication belongs to the current user
            // 2. the current user has faculty/course director/developer role
            case self::VIEW:
                return (
                    $user->getId() === $authentication->getUser()->getId()
                    || $user->hasRole(['Developer'])
                );
                break;
            // at least one of these must be true.
            // 1. the current user has developer role
            //    and has the same primary school affiliation as the given user
            // 2. the current user has developer role
            //    and has WRITE rights to one of the users affiliated schools.
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                $allSchoolIds = $authentication->getUser()->getAllSchools()->map(function (SchoolInterface $school) {
                    return $school->getId();
                });
                return (
                    $user->hasRole(['Developer'])
                    && (
                        $allSchoolIds->contains($user->getSchoolId())
                        || $user->hasWritePermissionToSchools($allSchoolIds->toArray())
                    )
                );
                break;
        }

        return false;
    }
}
