<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\CoreBundle\Entity\SchoolInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class SchoolEntityVoter
 */
class SchoolEntityVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof SchoolInterface && in_array($attribute, array(
            self::VIEW, self::CREATE, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param SchoolInterface $school
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $school, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // grant view access on schools to all authn. users.
                return true;
                break;
            case self::CREATE:
                // only developers can create schools.
                return $user->hasRole(['Developer']);
                break;
            case self::EDIT:
            case self::DELETE:
                // Only grant EDIT and DELETE permissions if the user has the 'Developer' role.
                // - and -
                // the user must be associated with the given school,
                // either by its primary school attribute
                //     - or - by WRITE rights for the school
                // via the permissions system.
                return (
                    $user->hasRole(['Developer'])
                    && (
                        $user->isThePrimarySchool($school)
                        || $user->hasWritePermissionToSchool($school->getId())
                    )
                );
                break;
        }

        return false;
    }
}
