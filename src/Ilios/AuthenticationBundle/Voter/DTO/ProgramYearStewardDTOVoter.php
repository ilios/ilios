<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\AuthenticationBundle\Voter\CourseVoter;
use Ilios\CoreBundle\Entity\DTO\ProgramYearStewardDTO;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class ProgramYearStewardDTOVoter
 */
class ProgramYearStewardDTOVoter extends CourseVoter
{
    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof ProgramYearStewardDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param ProgramYearStewardDTO $programYearSteward
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $programYearSteward, TokenInterface $token)
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
                            $user->getSchoolId() === $programYearSteward->owningSchool
                            || $user->hasReadPermissionToSchool($programYearSteward->owningSchool)
                            || $user->getSchoolId() === $programYearSteward->school
                        )
                    )
                    || $user->hasReadPermissionToProgram($programYearSteward->owningProgram)
                );

                break;
        }
        return false;
    }
}
