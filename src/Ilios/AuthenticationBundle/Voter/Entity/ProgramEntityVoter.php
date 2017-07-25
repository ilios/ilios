<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\CoreBundle\Entity\ProgramInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class ProgramEntityVoter
 */
class ProgramEntityVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof ProgramInterface && in_array($attribute, array(
            self::VIEW, self::CREATE, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param ProgramInterface $program
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $program, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // do not enforce special views permissions on programs.
                return true;
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                // the given user is granted CREATE, EDIT and DELETE permissions on the given program
                // when at least one of the following statements is true
                // 1. The user's primary school is the same as the program's owning school
                //    and the user has at least one of 'Course Director' and 'Developer' role.
                // 2. The user has WRITE permissions on the program's owning school
                //    and the user has at least one of 'Course Director' and 'Developer' role.
                // 3. The user has WRITE permissions on the program.
                return (
                    (
                        $user->hasRole(['Course Director', 'Developer'])
                        && (
                            $user->isThePrimarySchool($program->getSchool())
                            || $user->hasWritePermissionToSchool($program->getSchool()->getId())
                        )
                    )
                    || $user->hasWritePermissionToProgram($program->getId())
                );
                break;
        }

        return false;
    }
}
