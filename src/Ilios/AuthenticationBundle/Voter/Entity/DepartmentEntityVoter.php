<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\DepartmentInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class DepartmentEntityVoter
 */
class DepartmentEntityVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof DepartmentInterface && in_array($attribute, [
            self::VIEW, self::CREATE, self::EDIT, self::DELETE
        ]);
    }

    /**
     * @param string $attribute
     * @param DepartmentInterface $department
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $department, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // grant view access on departments to all authn. users.
                return true;
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                // grant CREATE, EDIT and DELETE privileges
                // if the user has the 'Developer' role
                // - and -
                //   if the user's primary school is the the department's owning school
                //   - or -
                //   if the user has WRITE rights on the departments's owning school
                // via the permissions system.
                return (
                    $user->hasRole(['Developer'])
                    && (
                        $user->isThePrimarySchool($department->getSchool())
                        || $user->hasWritePermissionToSchool($department->getSchool()->getId())
                    )
                );
                break;
        }

        return false;
    }
}
