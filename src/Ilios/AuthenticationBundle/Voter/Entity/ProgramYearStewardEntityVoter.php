<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\ProgramYearStewardInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class ProgramYearStewardVoter
 */
class ProgramYearStewardEntityVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof ProgramYearStewardInterface && in_array($attribute, array(
            self::VIEW, self::CREATE, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param ProgramYearStewardInterface $steward
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $steward, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // the given user is granted VIEW permissions on the given steward
                // when at least one of the following statements is true
                // 1. The user's primary school is the same as the parent program's owning school
                //    and the user has at least one of 'Course Director', 'Faculty' and 'Developer' role.
                // 2. The user has READ permissions on the parent program's owning school
                //    and the user has at least one of 'Course Director', 'Faculty' and 'Developer' role.
                // 3. The user's primary school matches the stewarding school
                //    and the user has at least one of 'Course Director', 'Faculty' and 'Developer' role.
                // 4. The user has READ permissions on the owning program.
                return (
                    (
                        $user->hasRole(['Course Director', 'Developer', 'Faculty'])
                        && (
                            $user->isThePrimarySchool($steward->getProgramOwningSchool())
                            || $user->hasReadPermissionToSchool($steward->getProgramOwningSchool()->getId())
                            || $user->isThePrimarySchool($steward->getSchool())
                        )
                    )
                    || $user->hasReadPermissionToProgram($steward->getProgram()->getId())
                );
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                // the given user is granted CREATE, EDIT and DELETE permissions on the given steward
                // when at least one of the following statements is true
                // 1. The user's primary school is the same as the parent program's owning school
                //    and the user has at least one of 'Course Director' and 'Developer' role.
                // 2. The user has WRITE permissions on the parent program's owning school
                //    and the user has at least one of 'Course Director' and 'Developer' role.
                // 3. The user's primary school matches the stewarding school
                //    and the user has at least one of 'Course Director' and 'Developer' role.
                // 4. The user has WRITE permissions on the parent program.
                return (
                    (
                        $user->hasRole(['Course Director', 'Developer'])
                        && (
                            $user->isThePrimarySchool($steward->getProgramOwningSchool())
                            || $user->hasWritePermissionToSchool($steward->getProgramOwningSchool()->getId())
                            || $user->isThePrimarySchool($steward->getSchool())
                        )
                    )
                    || $user->hasWritePermissionToProgram($steward->getProgram()->getId())
                );
                break;
        }

        return false;
    }
}
